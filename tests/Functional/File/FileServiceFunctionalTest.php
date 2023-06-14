<?php

/**
 * LICENSE: The MIT License (the "License")
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * https://github.com/azure/azure-storage-php/LICENSE
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * PHP version 5
 *
 * @see      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Tests\Functional\File;

use AzureOSS\Storage\Common\Exceptions\ServiceException;
use AzureOSS\Storage\Common\Internal\Utilities;
use AzureOSS\Storage\Common\Middlewares\HistoryMiddleware;
use AzureOSS\Storage\Common\Middlewares\RetryMiddlewareFactory;
use AzureOSS\Storage\Common\Models\Range;
use AzureOSS\Storage\File\FileRestProxy;
use AzureOSS\Storage\File\Models\CreateFileFromContentOptions;
use AzureOSS\Storage\File\Models\CreateShareOptions;
use AzureOSS\Storage\File\Models\FileServiceOptions;
use AzureOSS\Storage\File\Models\GetFileOptions;
use AzureOSS\Storage\File\Models\ListDirectoriesAndFilesOptions;
use AzureOSS\Storage\File\Models\ListSharesOptions;
use AzureOSS\Storage\File\Models\PutFileRangeOptions;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use MicrosoftAzure\Storage\Tests\Framework\TestResources;

class FileServiceFunctionalTest extends FunctionalTestBase
{
    public function testGetServicePropertiesNoOptions()
    {
        $serviceProperties = FileServiceFunctionalTestData::getDefaultServiceProperties();
        $this->restProxy->setServiceProperties($serviceProperties);
        $this->getServicePropertiesWorker(null);
    }

    public function testGetServiceProperties()
    {
        $serviceProperties = FileServiceFunctionalTestData::getDefaultServiceProperties();

        $shouldReturn = false;
        try {
            $this->restProxy->setServiceProperties($serviceProperties);
            self::assertFalse($this->isEmulated(), 'Should succeed when not running in emulator');
        } catch (ServiceException $e) {
            // Expect failure in emulator, as v1.6 doesn't support this method
            if ($this->isEmulated()) {
                self::assertEquals(TestResources::STATUS_BAD_REQUEST, $e->getCode(), 'getCode');
                $shouldReturn = true;
            } else {
                throw $e;
            }
        }
        if ($shouldReturn) {
            return;
        }

        // Now look at the combos.
        $interestingTimeouts = FileServiceFunctionalTestData::getInterestingTimeoutValues();
        foreach ($interestingTimeouts as $timeout) {
            $options = new FileServiceOptions();
            $options->setTimeout($timeout);
            $this->getServicePropertiesWorker($options);
        }
    }

    private function getServicePropertiesWorker($options)
    {
        $options = (null === $options ? new FileServiceOptions() : $options);
        try {
            $ret = $this->restProxy->getServiceProperties($options);

            if (null !== $options->getTimeout() && $options->getTimeout() < 1) {
                self::assertTrue(false, 'Expect negative timeouts in $options to throw');
            }
            $this->verifyServicePropertiesWorker($ret, null);
        } catch (ServiceException $e) {
            if (null === $options->getTimeout() || $options->getTimeout() >= 1) {
                throw $e;
            }
            self::assertEquals(
                TestResources::STATUS_INTERNAL_SERVER_ERROR,
                $e->getCode(),
                'getCode'
            );

        }
    }

    private function verifyServicePropertiesWorker($ret, $serviceProperties)
    {
        if (null === $serviceProperties) {
            $serviceProperties = FileServiceFunctionalTestData::getDefaultServiceProperties();
        }

        $sp = $ret->getValue();
        self::assertNotNull($sp, 'getValue should be non-null');

        $m = $sp->getHourMetrics();
        self::assertNotNull(
            $m,
            'getValue()->getHourMetrics() should be non-null'
        );
        self::assertEquals(
            $serviceProperties->getHourMetrics()->getVersion(),
            $m->getVersion(),
            'getValue()->getHourMetrics()->getVersion'
        );
        self::assertEquals(
            $serviceProperties->getHourMetrics()->getEnabled(),
            $m->getEnabled(),
            'getValue()->getHourMetrics()->getEnabled'
        );
        self::assertEquals(
            $serviceProperties->getHourMetrics()->getIncludeAPIs(),
            $m->getIncludeAPIs(),
            'getValue()->getHourMetrics()->getIncludeAPIs'
        );

        $r = $m->getRetentionPolicy();
        self::assertNotNull(
            $r,
            'getValue()->getHourMetrics()->getRetentionPolicy should be non-null'
        );
        self::assertEquals(
            $serviceProperties->getHourMetrics()->getRetentionPolicy()->getDays(),
            $r->getDays(),
            'getValue()->getHourMetrics()->getRetentionPolicy()->getDays'
        );
    }

    public function testSetServicePropertiesNoOptions()
    {
        $serviceProperties = FileServiceFunctionalTestData::getDefaultServiceProperties();
        $this->setServicePropertiesWorker($serviceProperties, null);
    }

    public function testSetServiceProperties()
    {
        $interestingServiceProperties =
            FileServiceFunctionalTestData::getInterestingServiceProperties();
        foreach ($interestingServiceProperties as $serviceProperties) {
            $interestingTimeouts = FileServiceFunctionalTestData::getInterestingTimeoutValues();
            foreach ($interestingTimeouts as $timeout) {
                $options = new FileServiceOptions();
                $options->setTimeout($timeout);
                $this->setServicePropertiesWorker($serviceProperties, $options);
            }
        }
    }

    private function setServicePropertiesWorker($serviceProperties, $options)
    {
        try {
            if (null === $options) {
                $this->restProxy->setServiceProperties($serviceProperties);
            } else {
                $this->restProxy->setServiceProperties($serviceProperties, $options);
            }

            if (null === $options) {
                $options = new FileServiceOptions();
            }

            if (null !== $options->getTimeout() && $options->getTimeout() < 1) {
                self::assertTrue(false, 'Expect negative timeouts in $options to throw');
            }

            \sleep(10);

            $ret = $this->restProxy->getServiceProperties($options);
            $this->verifyServicePropertiesWorker($ret, $serviceProperties);
        } catch (ServiceException $e) {
            if (null === $options) {
                $options = new FileServiceOptions();
            }

            if ($this->isEmulated()) {
                if (null !== $options->getTimeout() && $options->getTimeout() < 1) {
                    self::assertEquals(TestResources::STATUS_INTERNAL_SERVER_ERROR, $e->getCode(), 'getCode');
                } else {
                    self::assertEquals(TestResources::STATUS_BAD_REQUEST, $e->getCode(), 'getCode');
                }
            } else {
                if (null !== $options->getTimeout() && $options->getTimeout() < 1) {
                    self::assertEquals(TestResources::STATUS_INTERNAL_SERVER_ERROR, $e->getCode(), 'getCode');
                } else {
                    throw $e;
                }
            }
        }
    }

    public function testListSharesNoOptions()
    {
        $this->listSharesWorker(null);
    }

    public function testListShares()
    {
        $interestingListSharesOptions = FileServiceFunctionalTestData::getInterestingListSharesOptions();
        foreach ($interestingListSharesOptions as $options) {
            $this->listSharesWorker($options);
        }
    }

    private function listSharesWorker($options)
    {
        $finished = false;
        while (!$finished) {
            try {
                $ret = (
                    null === $options ?
                    $this->restProxy->listShares() :
                    $this->restProxy->listShares($options)
                );

                if (null === $options) {
                    $options = new ListSharesOptions();
                }

                if (null !== $options->getTimeout() && $options->getTimeout() < 1) {
                    self::assertTrue(false, 'Expect negative timeouts in $options to throw');
                }
                $this->verifyListSharesWorker($ret, $options);

                if (strlen($ret->getNextMarker()) == 0) {
                    $finished = true;
                } else {
                    $options->setMarker($ret->getNextMarker());
                }
            } catch (ServiceException $e) {
                $finished = true;
                if (null === $options->getTimeout() || $options->getTimeout() >= 1) {
                    throw $e;
                }
                self::assertEquals(TestResources::STATUS_INTERNAL_SERVER_ERROR, $e->getCode(), 'getCode');

            }
        }
    }

    private function verifyListSharesWorker($ret, $options)
    {
        // Cannot really check the next marker. Just make sure it is not null.
        self::assertEquals($options->getNextMarker(), $ret->getMarker(), 'getNextMarker');
        self::assertEquals($options->getMaxResults(), $ret->getMaxResults(), 'getMaxResults');
        self::assertEquals($options->getPrefix(), $ret->getPrefix(), 'getPrefix');

        self::assertNotNull($ret->getShares(), 'getFiles');
        if ($options->getMaxResults() == 0) {
            self::assertEquals(
                0,
                strlen($ret->getNextMarker()),
                'When MaxResults is 0, expect getNextMarker (' .
                    strlen($ret->getNextMarker()) . ')to be  '
            );

            if (null !== $options->getPrefix()
                && $options->getPrefix() ==
                    (FileServiceFunctionalTestData::$nonExistFilePrefix)) {
                self::assertCount(
                    0,
                    $ret->getShares(),
                    'when MaxResults=0 and Prefix=(\'' .
                        $options->getPrefix() . '\'), then Files length'
                );
            } elseif (null !== $options->getPrefix()
                && $options->getPrefix() ==
                    (FileServiceFunctionalTestData::$testUniqueId)) {
                self::assertEquals(
                    FileServiceFunctionalTestData::$trackedShareCount,
                    count(
                        $ret->getShares()
                    ),
                    'when MaxResults=0 and Prefix=(\'' .
                        $options->getPrefix() .
                        '\'), then Files length'
                );
            }
            // Do not know how many there should be

        } elseif (strlen($ret->getNextMarker()) == 0) {
            self::assertTrue(
                count($ret->getShares()) <= $options->getMaxResults(),
                'when NextMarker (\'' . $ret->getNextMarker() . '\')==\'\',
                Files length (' . count($ret->getShares()) .
                ') should be <= MaxResults (' . $options->getMaxResults() .
                ')'
            );

            if (FileServiceFunctionalTestData::$nonExistFilePrefix ==
                    $options->getPrefix()) {
                self::assertCount(
                    0,
                    $ret->getShares(),
                    'when no next marker and Prefix=(\'' .
                        $options->getPrefix() . '\'), then Files length'
                );
            } elseif (FileServiceFunctionalTestData::$testUniqueId ==
                    $options->getPrefix()) {
                // Need to futz with the mod because you are allowed to get MaxResults items returned.
                $expectedCount =
                    FileServiceFunctionalTestData::$trackedShareCount % $options->getMaxResults();
                self::assertCount(
                    $expectedCount,
                    $ret->getShares(),
                    'when no next marker and Prefix=(\'' .
                    $options->getPrefix() . '\'), then Files length'
                );
            }
            // Do not know how many there should be

        } else {
            self::assertEquals(
                count($ret->getShares()),
                $options->getMaxResults(),
                'when NextMarker (' . $ret->getNextMarker() .
                ')!=\'\', Files length (' . count($ret->getShares()) .
                ') should be == MaxResults (' . $options->getMaxResults() .
                ')'
            );
            if (null !== $options->getPrefix()
                && $options->getPrefix() ==
                    FileServiceFunctionalTestData::$nonExistFilePrefix) {
                self::assertTrue(
                    false,
                    'when a next marker and Prefix=(\'' .
                        $options->getPrefix() . '\'), impossible'
                );
            }
        }
    }

    public function testCreateShareNoOptions()
    {
        $this->createShareWorker(null);
    }

    public function testCreateShare()
    {
        $interestingCreateShareOptions = FileServiceFunctionalTestData::getInterestingCreateShareOptions();
        foreach ($interestingCreateShareOptions as $options) {
            $this->createShareWorker($options);
        }
    }

    private function createShareWorker($options)
    {
        $share = FileServiceFunctionalTestData::getInterestingShareName();
        $created = false;

        try {
            if (null === $options) {
                $this->restProxy->createShare($share);
            } else {
                $this->restProxy->createShare($share, $options);
            }
            $created = true;

            if (null === $options) {
                $options = new CreateShareOptions();
            }

            if (null !== $options->getTimeout() && $options->getTimeout() < 1) {
                self::assertTrue(false, 'Expect negative timeouts in $options to throw');
            }

            // Now check that the $share was $created correctly.

            // Make sure that the list of all applicable shares is correctly updated.
            $opts = new ListSharesOptions();
            $opts->setPrefix(FileServiceFunctionalTestData::$testUniqueId);
            $qs = $this->restProxy->listShares($opts);
            self::assertEquals(
                count($qs->getShares()),
                FileServiceFunctionalTestData::$trackedShareCount + 1,
                'After adding one, with Prefix=(\'' . FileServiceFunctionalTestData::$testUniqueId .
                    '\'), then Shares length'
            );

            // Check the metadata on the share
            $ret = $this->restProxy->getShareMetadata($share);
            $this->verifyCreateShareWorker($ret, $options);
            $this->restProxy->deleteShare($share);
        } catch (ServiceException $e) {
            if (null === $options) {
                $options = new CreateShareOptions();
            }

            if (null === $options->getTimeout() || $options->getTimeout() >= 1) {
                throw $e;
            }
            self::assertEquals(TestResources::STATUS_INTERNAL_SERVER_ERROR, $e->getCode(), 'getCode');

        }

        if ($created) {
            try {
                $this->restProxy->deleteShare($share);
            } catch (ServiceException $e) {
                // Ignore.
            }
        }
    }

    private function verifyCreateShareWorker($ret, $options)
    {
        if (null === $options->getMetadata()) {
            self::assertNotNull($ret->getMetadata(), 'share Metadata');
            self::assertCount(0, $ret->getMetadata(), 'count share Metadata');
        } else {
            self::assertNotNull($ret->getMetadata(), 'share Metadata');
            self::assertEquals(count($options->getMetadata()), count($ret->getMetadata()), 'Metadata');
            $retMetadata = $ret->getMetadata();
            foreach ($options->getMetadata() as $key => $value) {
                self::assertEquals($value, $retMetadata[$key], 'Metadata(' . $key . ')');
            }
        }
    }

    public function testDeleteShareNoOptions()
    {
        $this->deleteShareWorker(null);
    }

    public function testDeleteShare()
    {
        $interestingDeleteShareOptions = FileServiceFunctionalTestData::getInterestingDeleteShareOptions();
        foreach ($interestingDeleteShareOptions as $options) {
            $this->deleteShareWorker($options);
        }
    }

    private function deleteShareWorker($options)
    {
        $share = FileServiceFunctionalTestData::getInterestingShareName();

        // Make sure there is something to delete.
        $this->restProxy->createShare($share);

        // Make sure that the list of all applicable shares is correctly updated.
        $opts = new ListSharesOptions();
        $opts->setPrefix(FileServiceFunctionalTestData::$testUniqueId);
        $qs = $this->restProxy->listShares($opts);
        self::assertEquals(
            count($qs->getShares()),
            FileServiceFunctionalTestData::$trackedShareCount + 1,
            'After adding one, with Prefix=(\'' .
                FileServiceFunctionalTestData::$testUniqueId .
                '\'), then Shares length'
        );

        $deleted = false;
        try {
            if (null === $options) {
                $this->restProxy->deleteShare($share);
            } else {
                $this->restProxy->deleteShare($share, $options);
            }

            $deleted = true;

            if (null === $options) {
                $options = new FileServiceOptions();
            }

            if (null !== $options->getTimeout() && $options->getTimeout() < 1) {
                self::assertTrue(false, 'Expect negative timeouts in $options to throw');
            }

            // Make sure that the list of all applicable shares is correctly updated.
            $opts = new ListSharesOptions();
            $opts->setPrefix(FileServiceFunctionalTestData::$testUniqueId);
            $qs = $this->restProxy->listShares($opts);
            self::assertEquals(
                count($qs->getShares()),
                FileServiceFunctionalTestData::$trackedShareCount,
                'After adding then deleting one, with Prefix=(\'' .
                    FileServiceFunctionalTestData::$testUniqueId .
                    '\'), then Shares length'
            );

            // Nothing else interesting to check for the $options.
        } catch (ServiceException $e) {
            if (null === $options) {
                $options = new FileServiceOptions();
            }

            if (null !== $options->getTimeout() && $options->getTimeout() < 1) {
                self::assertEquals(TestResources::STATUS_INTERNAL_SERVER_ERROR, $e->getCode(), 'getCode');
            } elseif (!$this->isEmulated()
                    && !FileServiceFunctionalTestData::passTemporalAccessCondition(
                        $options->getAccessConditions()
                    )) {
                self::assertEquals(TestResources::STATUS_PRECONDITION_FAILED, $e->getCode(), 'getCode');
            } else {
                throw $e;
            }
        }

        if (!$deleted) {
            // Try again. If it does not work, not much else to try.
            $this->restProxy->deleteShare($share);
        }
    }

    public function testGetShareMetadataNoOptions()
    {
        $metadata = FileServiceFunctionalTestData::getNiceMetadata();
        $this->getShareMetadataWorker(null, $metadata);
    }

    public function testGetShareMetadata()
    {
        $interestingTimeouts = FileServiceFunctionalTestData::getInterestingTimeoutValues();
        $metadata = FileServiceFunctionalTestData::getNiceMetadata();

        foreach ($interestingTimeouts as $timeout) {
            $options = new FileServiceOptions();
            $options->setTimeout($timeout);
            $this->getShareMetadataWorker($options, $metadata);
        }
    }

    private function getShareMetadataWorker($options, $metadata)
    {
        $share = FileServiceFunctionalTestData::getInterestingShareName();

        // Make sure there is something to test
        $this->restProxy->createShare($share);
        $this->restProxy->setShareMetadata($share, $metadata);

        try {
            $res = (null === $options ?
                $this->restProxy->getShareMetadata($share) :
                $this->restProxy->getShareMetadata($share, $options));

            if (null === $options) {
                $options = new FileServiceOptions();
            }

            if (null !== $options->getTimeout() && $options->getTimeout() <= 0) {
                self::assertTrue(false, 'Expect negative timeouts in $options to throw');
            }

            $this->verifyGetShareMetadataWorker($res, $metadata);
        } catch (ServiceException $e) {
            if (null === $options->getTimeout() || $options->getTimeout() > 0) {
                throw $e;
            }
            self::assertEquals(TestResources::STATUS_INTERNAL_SERVER_ERROR, $e->getCode(), 'getCode');

        }
        // Clean up.
        $this->restProxy->deleteShare($share);
    }

    private function verifyGetShareMetadataWorker($ret, $metadata)
    {
        self::assertNotNull($ret->getMetadata(), 'share Metadata');
        self::assertNotNull($ret->getETag(), 'share getETag');
        self::assertNotNull($ret->getLastModified(), 'share getLastModified');

        self::assertEquals(count($metadata), count($ret->getMetadata()), 'Metadata');
        $md = $ret->getMetadata();
        foreach ($metadata as $key => $value) {
            self::assertEquals($value, $md[$key], 'Metadata(' . $key . ')');
        }

        // Make sure the last modified date is within 10 seconds
        $now = new \DateTime();
        self::assertTrue(
            FileServiceFunctionalTestData::diffInTotalSeconds(
                $ret->getLastModified(),
                $now
            ) < 10,
            'Last modified date (' .
            $ret->getLastModified()->format(\DateTime::RFC1123) .
                ')' . ' should be within 10 seconds of $now (' .
                $now->format(\DateTime::RFC1123) . ')'
        );
    }

    public function testSetShareMetadataNoOptions()
    {
        $interestingMetadata = FileServiceFunctionalTestData::getInterestingMetadata();
        foreach ($interestingMetadata as $metadata) {
            $this->setShareMetadataWorker(null, $metadata);
        }
    }

    public function testSetShareMetadata()
    {
        $interestingSetShareMetadataOptions = FileServiceFunctionalTestData::getFileServiceOptions();
        $interestingMetadata = FileServiceFunctionalTestData::getInterestingMetadata();

        foreach ($interestingSetShareMetadataOptions as $options) {
            foreach ($interestingMetadata as $metadata) {
                $this->setShareMetadataWorker($options, $metadata);
            }
        }
    }

    private function setShareMetadataWorker($options, $metadata)
    {
        $share = FileServiceFunctionalTestData::getInterestingShareName();

        // Make sure there is something to test
        $this->restProxy->createShare($share);

        $firstkey = '';
        if (null !== $metadata && count($metadata) > 0) {
            $firstkey = array_keys($metadata);
            $firstkey = $firstkey[0];
        }

        try {
            // And put in some metadata
            if (null === $options) {
                $this->restProxy->setShareMetadata($share, $metadata);
            } else {
                $this->restProxy->setShareMetadata($share, $metadata, $options);
            }

            if (null === $options) {
                $options = new FileServiceOptions();
            }

            self::assertFalse(
                Utilities::startsWith($firstkey, '<'),
                'Should get HTTP request error if the metadata is invalid'
            );

            if (null !== $options->getTimeout() && $options->getTimeout() < 1) {
                self::assertTrue(false, 'Expect negative timeouts in $options to throw');
            }

            $res = $this->restProxy->getShareMetadata($share);
            $this->verifyGetShareMetadataWorker($res, $metadata);
        } catch (ServiceException $e) {
            if (Utilities::startsWith($firstkey, '<')) {
                self::assertEquals(TestResources::STATUS_BAD_REQUEST, $e->getCode(), 'getCode');
            } elseif (null !== $options->getTimeout() && $options->getTimeout() < 1) {
                self::assertEquals(TestResources::STATUS_INTERNAL_SERVER_ERROR, $e->getCode(), 'getCode');
            } else {
                throw $e;
            }
        }

        // Clean up.
        $this->restProxy->deleteShare($share);
    }

    public function testGetSharePropertiesNoOptions()
    {
        $metadata = FileServiceFunctionalTestData::getNiceMetadata();
        $this->getSharePropertiesWorker(null, $metadata);
    }

    public function testGetShareProperties()
    {
        $interestingTimeouts = FileServiceFunctionalTestData::getInterestingTimeoutValues();
        $metadata = FileServiceFunctionalTestData::getNiceMetadata();
        foreach ($interestingTimeouts as $timeout) {
            $options = new FileServiceOptions();
            $options->setTimeout($timeout);
            $this->getSharePropertiesWorker($options, $metadata);
        }
    }

    private function getSharePropertiesWorker($options, $metadata)
    {
        $share = FileServiceFunctionalTestData::getInterestingShareName();

        // Make sure there is something to test
        $this->restProxy->createShare($share);
        $this->restProxy->setShareMetadata($share, $metadata);

        try {
            $res = (null === $options ?
                $this->restProxy->getShareProperties($share) :
                $this->restProxy->getShareProperties($share, $options));

            if (null === $options) {
                $options = new FileServiceOptions();
            }

            if (null !== $options->getTimeout() && $options->getTimeout() < 1) {
                self::assertTrue(false, 'Expect negative timeouts in $options to throw');
            }

            $this->verifyGetShareMetadataWorker($res, $metadata);
        } catch (ServiceException $e) {
            if (null === $options->getTimeout() || $options->getTimeout() >= 1) {
                throw $e;
            }
            self::assertEquals(TestResources::STATUS_INTERNAL_SERVER_ERROR, $e->getCode(), 'getCode');

        }

        // Clean up.
        $this->restProxy->deleteShare($share);
    }

    public function testGetShareACLNoOptions()
    {
        $this->getShareACLWorker(null);
    }

    public function testGetShareACL()
    {
        $interestingTimeouts = FileServiceFunctionalTestData::getInterestingTimeoutValues();
        foreach ($interestingTimeouts as $timeout) {
            $options = new FileServiceOptions();
            $options->setTimeout($timeout);
            $this->getShareACLWorker($options);
        }
    }

    private function getShareACLWorker($options)
    {
        $share = FileServiceFunctionalTestData::getInterestingShareName();

        // Make sure there is something to test
        $this->restProxy->createShare($share);

        try {
            $res = (null === $options ?
                $this->restProxy->getShareACL($share) :
                $this->restProxy->getShareACL($share, $options));

            if (null === $options) {
                $options = new FileServiceOptions();
            }

            if (null !== $options->getTimeout() && $options->getTimeout() < 1) {
                self::assertTrue(false, 'Expect negative timeouts in $options to throw');
            }

            $this->verifyGetShareACLWorker($res);
        } catch (ServiceException $e) {
            if (null === $options->getTimeout() || $options->getTimeout() >= 1) {
                throw $e;
            }
            self::assertEquals(TestResources::STATUS_INTERNAL_SERVER_ERROR, $e->getCode(), 'getCode');

        }

        // Clean up.
        $this->restProxy->deleteShare($share);
    }

    private function verifyGetShareACLWorker($ret)
    {
        self::assertNotNull($ret->getShareACL(), '$ret->getShareACL');
        self::assertNotNull($ret->getETag(), '$ret->getETag');
        self::assertNotNull($ret->getLastModified(), '$ret->getLastModified');
        self::assertNotNull(
            $ret->getShareACL()->getSignedIdentifiers(),
            '$ret->getShareACL->getSignedIdentifiers'
        );

        // Make sure the last modified date is within 10 seconds
        $now = new \DateTime();
        self::assertTrue(
            FileServiceFunctionalTestData::diffInTotalSeconds(
                $ret->getLastModified(),
                $now
            ) < 10000,
            'Last modified date (' .
                $ret->getLastModified()->format(\DateTime::RFC1123) .
                ') ' . 'should be within 10 seconds of $now (' .
                $now->format(\DateTime::RFC1123) . ')'
        );
    }

    public function testSetShareACLNoOptions()
    {
        $interestingACL = FileServiceFunctionalTestData::getInterestingACL();
        foreach ($interestingACL as $acl) {
            $this->setShareACLWorker(null, $acl);
        }
    }

    public function testSetShareACL()
    {
        $interestingACL = FileServiceFunctionalTestData::getInterestingACL();
        $interestingTimeouts = FileServiceFunctionalTestData::getInterestingTimeoutValues();
        foreach ($interestingTimeouts as $timeout) {
            foreach ($interestingACL as $acl) {
                $options = new FileServiceOptions();
                $options->setTimeout($timeout);
                $this->setShareACLWorker($options, $acl);
            }
        }
    }

    private function setShareACLWorker($options, $acl)
    {
        $share = FileServiceFunctionalTestData::getInterestingShareName();

        // Make sure there is something to test
        $this->restProxy->createShare($share);
        $fileContent = uniqid();
        $this->restProxy->createFileFromContent($share, 'test', $fileContent);

        try {
            if (null === $options) {
                $this->restProxy->setShareACL($share, $acl);
                $this->restProxy->setShareACL($share, $acl);
            } else {
                $this->restProxy->setShareACL($share, $acl, $options);
                $this->restProxy->setShareACL($share, $acl, $options);
            }

            if (null === $options) {
                $options = new FileServiceOptions();
            }

            if (null !== $options->getTimeout() && $options->getTimeout() < 1) {
                self::assertTrue(false, 'Expect negative timeouts in $options to throw');
            }

            $res = $this->restProxy->getShareACL($share);
            $this->verifySetShareACLWorker($res, $share, $acl, $fileContent);
        } catch (ServiceException $e) {
            if (null !== $options->getTimeout() && $options->getTimeout() < 1) {
                self::assertEquals(TestResources::STATUS_INTERNAL_SERVER_ERROR, $e->getCode(), 'getCode');
            } else {
                throw $e;
            }
        }

        // Clean up.
        $this->restProxy->deleteShare($share);
    }

    private function verifySetShareACLWorker($ret, $share, $acl, $fileContent)
    {
        self::assertNotNull($ret->getShareACL(), '$ret->getShareACL');
        self::assertNotNull($ret->getETag(), '$ret->getShareACL->getETag');
        $now = new \DateTime();
        self::assertTrue(
            FileServiceFunctionalTestData::diffInTotalSeconds(
                $ret->getLastModified(),
                $now
            ) < 10000,
            'Last modified date (' .
                $ret->getLastModified()->format(\DateTime::RFC1123) .
                ') ' . 'should be within 10 seconds of $now (' .
                $now->format(\DateTime::RFC1123) . ')'
        );

        self::assertNotNull(
            $ret->getShareACL()->getSignedIdentifiers(),
            '$ret->getShareACL->getSignedIdentifiers'
        );

        $expIds = $acl->getSignedIdentifiers();
        $actIds = $ret->getShareACL()->getSignedIdentifiers();
        self::assertEquals(count($expIds), count($actIds), '$ret->getShareACL->getSignedIdentifiers');

        for ($i = 0; $i < count($expIds); ++$i) {
            $expId = $expIds[$i];
            $actId = $actIds[$i];
            self::assertEquals($expId->getId(), $actId->getId(), 'SignedIdentifiers[' . $i . ']->getId');
            self::assertEquals(
                $expId->getAccessPolicy()->getPermission(),
                $actId->getAccessPolicy()->getPermission(),
                'SignedIdentifiers[' . $i . ']->getAccessPolicy->getPermission'
            );
            self::assertTrue(
                FileServiceFunctionalTestData::diffInTotalSeconds(
                    $expId->getAccessPolicy()->getStart(),
                    $actId->getAccessPolicy()->getStart()
                ) < 1,
                'SignedIdentifiers[' . $i . ']->getAccessPolicy->getStart should match within 1 second, ' .
                    'exp=' . $expId->getAccessPolicy()->getStart()->format(\DateTime::RFC1123) . ', ' .
                    'act=' . $actId->getAccessPolicy()->getStart()->format(\DateTime::RFC1123)
            );
            self::assertTrue(
                FileServiceFunctionalTestData::diffInTotalSeconds(
                    $expId->getAccessPolicy()->getExpiry(),
                    $actId->getAccessPolicy()->getExpiry()
                ) < 1,
                'SignedIdentifiers[' . $i . ']->getAccessPolicy->getExpiry should match within 1 second, ' .
                    'exp=' . $expId->getAccessPolicy()->getExpiry()->format(\DateTime::RFC1123) . ', ' .
                    'act=' . $actId->getAccessPolicy()->getExpiry()->format(\DateTime::RFC1123)
            );
        }
    }

    private function prepareDirectoriesAndFiles($shareName, $directoriesCount, $filesCount)
    {
        for ($i = 0; $i < $directoriesCount; ++$i) {
            $this->restProxy->createDirectory(
                $shareName,
                FileServiceFunctionalTestData::getInterestingDirectoryName()
            );
        }
        for ($i = 0; $i < $filesCount; ++$i) {
            $this->restProxy->createFile(
                $shareName,
                FileServiceFunctionalTestData::getInterestingFileName(),
                \mt_rand(1, 100)
            );
        }
    }

    public function testListDirectoriesAndFilesNoOptions()
    {
        $share = FileServiceFunctionalTestData::getInterestingShareName();
        $this->safeCreateShare($share);
        $this->prepareDirectoriesAndFiles($share, 5, 5);
        $this->listDirectoriesAndFilesWorker($share, null);
        $this->safeDeleteShare($share);
    }

    public function testListDirectoriesAndFiles()
    {
        $interestingListFilesOptions =
            FileServiceFunctionalTestData::getInterestingListDirectoriesAndFilesOptions();
        $share = FileServiceFunctionalTestData::getInterestingShareName();
        $this->safeCreateShare($share);
        $this->prepareDirectoriesAndFiles($share, 10, 10);
        foreach ($interestingListFilesOptions as $options) {
            $this->listDirectoriesAndFilesWorker($share, $options);
        }
        $this->safeDeleteShare($share);
    }

    private function listDirectoriesAndFilesWorker($share, $options)
    {
        $finished = false;
        while (!$finished) {
            try {
                $ret = (null === $options ?
                    $this->restProxy->listDirectoriesAndFiles($share) :
                    $this->restProxy->listDirectoriesAndFiles($share, '', $options));

                if (null === $options) {
                    $options = new ListDirectoriesAndFilesOptions();
                }

                if (null !== $options->getTimeout() && $options->getTimeout() < 1) {
                    self::assertTrue(false, 'Expect negative timeouts in $options to throw');
                }
                $this->verifyListDirectoriesAndFilesWorker($ret, $options);

                if (strlen($ret->getNextMarker()) == 0) {
                    $finished = true;
                } else {
                    $options->setMarker($ret->getNextMarker());
                }
            } catch (ServiceException $e) {
                $finished = true;
                if (null === $options->getTimeout() || $options->getTimeout() >= 1) {
                    throw $e;
                }
                self::assertEquals(TestResources::STATUS_INTERNAL_SERVER_ERROR, $e->getCode(), 'getCode');

            }
        }
    }

    private function verifyListDirectoriesAndFilesWorker($ret, $options)
    {
        self::assertEquals($options->getMaxResults(), $ret->getMaxResults(), 'getMaxResults');

        self::assertNotNull($ret->getFiles(), 'getFiles');
        if ($options->getMaxResults() == 0) {
            self::assertEquals(
                0,
                strlen($ret->getNextMarker()),
                'When MaxResults is 0, expect getNextMarker (' .
                    strlen($ret->getNextMarker()) . ')to be  '
            );
        } elseif (strlen($ret->getNextMarker()) == 0) {
            self::assertTrue(
                count($ret->getFiles()) + count($ret->getDirectories()) <= $options->getMaxResults(),
                'when NextMarker (\'' . $ret->getNextMarker() .
                '\')==\'\', Files length (' .
                count($ret->getFiles()) + count($ret->getDirectories()) .
                    ') should be <= MaxResults (' .
                    $options->getMaxResults() . ')'
            );
        } else {
            self::assertEquals(
                $options->getMaxResults(),
                count($ret->getFiles()) + count($ret->getDirectories()),
                'when NextMarker (' . $ret->getNextMarker() .
                    ')!=\'\', Files length (' .
                    count($ret->getFiles()) + count($ret->getDirectories()) .
                    ') should be == MaxResults (' .
                    $options->getMaxResults() . ')'
            );
        }
    }

    public function testGetFileMetadataNoOptions()
    {
        $share = FileServiceFunctionalTestData::getInterestingShareName();
        $this->safeCreateShare($share);
        $this->getFileMetadataWorker($share, null);
        $this->safeDeleteShare($share);
    }

    public function testGetFileMetadata()
    {
        $interestingTimeouts = FileServiceFunctionalTestData::getInterestingTimeoutValues();

        foreach ($interestingTimeouts as $timeout) {
            $share = FileServiceFunctionalTestData::getInterestingShareName();
            $this->safeCreateShare($share);
            $options = new FileServiceOptions();
            $options->setTimeout($timeout);
            $this->getFileMetadataWorker($share, $options);
            $this->safeDeleteShare($share);
        }
    }

    private function getFileMetadataWorker($share, $options)
    {
        $file = FileServiceFunctionalTestData::getInterestingFileName($share);

        // Make sure there is something to test
        $testContent = \uniqid();
        $this->restProxy->createFileFromContent($share, $file, $testContent);

        $properties = FileServiceFunctionalTestData::getNiceMetadata();
        $this->restProxy->setFileMetadata($share, $file, $properties);

        try {
            $res = (
                null === $options ?
                $this->restProxy->getFileMetadata($share, $file) :
                $this->restProxy->getFileMetadata($share, $file, $options)
            );

            if (null === $options) {
                $options = new FileServiceOptions();
            }

            if (null !== $options->getTimeout() && ($options->getTimeout() < 1 || $options->getTimeout() == 2147483647)) {
                self::assertTrue(false, 'Expect negative timeouts in $options to throw');
            }
            $this->verifyGetFileMetadataWorker($res, $properties);
        } catch (ServiceException $e) {
            if (null !== $options->getTimeout() && ($options->getTimeout() < 1 || $options->getTimeout() == 2147483647)) {
                self::assertEquals(TestResources::STATUS_INTERNAL_SERVER_ERROR, $e->getCode(), 'bad timeout: getCode');
            } else {
                throw $e;
            }
        }
    }

    private function verifyGetFileMetadataWorker($res, $metadata)
    {
        self::assertNotNull($res->getMetadata(), 'file Metadata');
        self::assertNotNull($res->getETag(), 'file getETag');
        self::assertNotNull($res->getLastModified(), 'file getLastModified');

        self::assertEquals(count($metadata), count($res->getMetadata()), 'Metadata');
        $retMetadata = $res->getMetadata();
        foreach ($metadata as $key => $value) {
            self::assertEquals($value, $retMetadata[$key], 'Metadata(' . $key . ')');
        }

        // Make sure the last modified date is within 10 seconds
        $now = new \DateTime();
        self::assertTrue(
            FileServiceFunctionalTestData::diffInTotalSeconds(
                $res->getLastModified(),
                $now
            ) < 10000,
            'Last modified date (' .
                $res->getLastModified()->format(\DateTime::RFC1123) .
                ') ' . 'should be within 10 seconds of $now (' .
                $now->format(\DateTime::RFC1123) . ')'
        );
    }

    public function testSetFileMetadataNoOptions()
    {
        $interestingMetadata = FileServiceFunctionalTestData::getInterestingMetadata();
        foreach ($interestingMetadata as $properties) {
            $share = FileServiceFunctionalTestData::getInterestingShareName();
            $this->safeCreateShare($share);
            $this->setFileMetadataWorker($share, null, $properties);
            $this->safeDeleteShare($share);
        }
    }

    public function testSetFileMetadata()
    {
        $interestingSetFileMetadataOptions = FileServiceFunctionalTestData::getFileServiceOptions();
        $interestingMetadata = FileServiceFunctionalTestData::getInterestingMetadata();

        foreach ($interestingSetFileMetadataOptions as $options) {
            foreach ($interestingMetadata as $properties) {
                $share = FileServiceFunctionalTestData::getInterestingShareName();
                $this->safeCreateShare($share);
                $this->setFileMetadataWorker($share, $options, $properties);
                $this->safeDeleteShare($share);
            }
        }
    }

    private function setFileMetadataWorker($share, $options, $metadata)
    {
        $file = FileServiceFunctionalTestData::getInterestingFileName($share);

        // Make sure there is something to test
        $testContent = \uniqid();
        $this->restProxy->createFileFromContent($share, $file, $testContent);

        $firstkey = '';
        if (null !== $metadata && count($metadata) > 0) {
            $firstkey = array_keys($metadata);
            $firstkey = $firstkey[0];
        }

        try {
            // And put in some properties
            (null === $options ?
            $this->restProxy->setFileMetadata($share, $file, $metadata) :
            $this->restProxy->setFileMetadata(
                $share,
                $file,
                $metadata,
                $options
            ));
            if (null === $options) {
                $options = new FileServiceOptions();
            }

            if (null !== $options->getTimeout() && $options->getTimeout() < 1) {
                self::assertTrue(false, 'Expect negative timeouts in $options to throw');
            }
            if (Utilities::startsWith($firstkey, '<')) {
                self::assertTrue(false, 'Should get HTTP request error if the metadata is invalid');
            }

            $res = $this->restProxy->getFileMetadata($share, $file);
            $this->verifyGetFileMetadataWorker($res, $metadata);
        } catch (ServiceException $e) {
            if (Utilities::startsWith($firstkey, '<')) {
                self::assertEquals(TestResources::STATUS_BAD_REQUEST, $e->getCode(), 'getCode');
            } elseif (null !== $options->getTimeout() && $options->getTimeout() < 1) {
                self::assertEquals(TestResources::STATUS_INTERNAL_SERVER_ERROR, $e->getCode(), 'bad timeout: getCode');
            } else {
                throw $e;
            }
        }
    }

    private function verifySetFileMetadataWorker($res)
    {
        self::assertNotNull($res->getETag(), 'file getETag');
        self::assertNotNull($res->getLastModified(), 'file getLastModified');

        // Make sure the last modified date is within 10 seconds
        $now = new \DateTime();
        self::assertTrue(
            FileServiceFunctionalTestData::diffInTotalSeconds(
                $res->getLastModified(),
                $now
            ) < 10000,
            'Last modified date (' .
            $res->getLastModified()->format(\DateTime::RFC1123) . ') ' .
                'should be within 10 seconds of $now (' .
                $now->format(\DateTime::RFC1123) . ')'
        );
    }

    public function testGetSetFileProperties()
    {
        $interestingFileProperties =
            FileServiceFunctionalTestData::getSetFileProperties();

        foreach ($interestingFileProperties as $properties) {
            $share = FileServiceFunctionalTestData::getInterestingShareName();
            $this->safeCreateShare($share);
            $this->getSetFilePropertiesWorker($share, $properties);
            $this->safeDeleteShare($share);
        }
    }

    private function getSetFilePropertiesWorker($share, $properties)
    {
        $file = FileServiceFunctionalTestData::getInterestingFileName($share);

        // Make sure there is something to test
        $testContent = \uniqid();
        $this->restProxy->createFileFromContent($share, $file, $testContent);

        if ($properties->getContentLength() == null) {
            $properties->setContentLength(\strlen($testContent));
        }
        if ($properties->getContentType() == null) {
            $properties->setContentType('application/x-www-form-urlencoded');
        }
        $this->restProxy->setFileProperties($share, $file, $properties);

        $res = $this->restProxy->getFileProperties($share, $file);

        $this->verifyGetSetFilePropertiesWorker($res, $properties);
    }

    private function verifyGetSetFilePropertiesWorker($res, $properties)
    {
        self::assertEquals(
            $res->getContentLength(),
            $properties->getContentLength()
        );
        self::assertEquals(
            $res->getContentType(),
            $properties->getContentType()
        );
        self::assertEquals(
            $res->getContentMD5(),
            $properties->getContentMD5()
        );
        self::assertEquals(
            $res->getContentEncoding(),
            $properties->getContentEncoding()
        );
        self::assertEquals(
            $res->getContentLanguage(),
            $properties->getContentLanguage()
        );
        self::assertEquals(
            $res->getCacheControl(),
            $properties->getCacheControl()
        );
        self::assertEquals(
            $res->getContentDisposition(),
            $properties->getContentDisposition()
        );
        self::assertNotNull($res->getETag());

        // Make sure the last modified date is within 10 seconds
        $now = new \DateTime();
        self::assertTrue(
            FileServiceFunctionalTestData::diffInTotalSeconds(
                $res->getLastModified(),
                $now
            ) < 10000,
            'Last modified date (' .
                $res->getLastModified()->format(
                    \DateTime::RFC1123
                ) . ') ' .
                    'should be within 10 seconds of $now (' .
                    $now->format(\DateTime::RFC1123) . ')'
        );
    }

    public function testGetFileNoOptions()
    {
        $share = FileServiceFunctionalTestData::getInterestingShareName();
        $this->safeCreateShare($share);
        $this->getFileWorker(null, $share);
        $this->safeDeleteShare($share);
    }

    public function testGetFileAllOptions()
    {
        $interestingGetFileOptions = FileServiceFunctionalTestData::getGetFileOptions();
        foreach ($interestingGetFileOptions as $options) {
            $share = FileServiceFunctionalTestData::getInterestingShareName();
            $this->safeCreateShare($share);
            $this->getFileWorker($options, $share);
            $this->safeDeleteShare($share);
        }
    }

    private function getFileWorker($options, $share)
    {
        $file = FileServiceFunctionalTestData::getInterestingFileName();

        // Make sure there is something to test
        $dataSize = 512;
        $content = FileServiceFunctionalTestData::getRandomBytes($dataSize);
        $createFileOptions = new createFileFromContentOptions();
        $createFileOptions->setUseTransactionalMD5(true);
        if ($options && $options->getRangeGetContentMD5()) {
            $createFileOptions->setContentMD5('MDAwMDAwMDA=');
        }
        $this->restProxy->createFileFromContent($share, $file, $content, $createFileOptions);

        $metadata = FileServiceFunctionalTestData::getNiceMetadata();
        $sbmd = $this->restProxy->setFileMetadata($share, $file, $metadata);

        try {
            $res = (
                null === $options ?
                $this->restProxy->getFile($share, $file) :
                $this->restProxy->getFile($share, $file, $options)
            );

            if (null === $options) {
                $options = new GetFileOptions();
            }

            if (null !== $options->getTimeout() && $options->getTimeout() < 1) {
                self::assertTrue(false, 'Expect negative timeouts in $options to throw');
            }
            if ($options->getRangeGetContentMD5() && null === $options->getRangeString()) {
                self::assertTrue(false, 'Expect compute range MD5 to fail if range not set');
            }

            $this->verifyGetFileWorker($res, $options, $dataSize, $metadata);
        } catch (ServiceException $e) {
            if ($options->getRangeGetContentMD5() && null === $options->getRangeString()) {
                self::assertEquals(
                    TestResources::STATUS_BAD_REQUEST,
                    $e->getCode(),
                    'Expect compute range MD5 to fail when range not set:' .
                    ' getCode'
                );
            } elseif (null !== $options->getTimeout() && $options->getTimeout() < 1) {
                self::assertEquals(
                    TestResources::STATUS_INTERNAL_SERVER_ERROR,
                    $e->getCode(),
                    'bad timeout: getCode'
                );
            } else {
                throw $e;
            }
        }
    }

    private function verifyGetFileWorker($res, $options, $dataSize, $metadata)
    {
        self::assertNotNull($res, 'result');

        $content = stream_get_contents($res->getContentStream());

        $range = $options->getRange();
        if ($range == null) {
            $range = new Range(0);
        }
        $rangeSize = $range->getLength();
        if ($rangeSize == null) {
            $rangeSize = $dataSize - $range->getStart();
        }

        self::assertEquals(
            $rangeSize,
            \strlen($content),
            '$content length and range'
        );

        if ($options->getRangeGetContentMD5()) {
            self::assertEquals(
                'MDAwMDAwMDA=',
                $res->getProperties()->getContentMD5(),
                'asked for MD5, result->getProperties()->getContentMD5'
            );
        } else {
            self::assertNull(
                $res->getProperties()->getContentMD5(),
                'did not ask for MD5, result->getProperties()->getContentMD5'
            );
        }

        self::assertNotNull($res->getMetadata(), 'file Metadata');
        $resMetadata = $res->getMetadata();
        self::assertEquals(count($metadata), count($resMetadata), 'Metadata');
        foreach ($metadata as $key => $value) {
            self::assertEquals($value, $resMetadata[$key], 'Metadata(' . $key . ')');
        }
    }

    public function testDeleteFileNoOptions()
    {
        $share = FileServiceFunctionalTestData::getInterestingShareName();
        $this->safeCreateShare($share);
        $this->deleteFileWorker(null, $share);
        $this->safeDeleteShare($share);
    }

    public function testDeleteFile()
    {
        $interestingDeleteFileOptions = FileServiceFunctionalTestData::getFileServiceOptions();
        foreach ($interestingDeleteFileOptions as $options) {
            $share = FileServiceFunctionalTestData::getInterestingShareName();
            $this->safeCreateShare($share);
            $this->deleteFileWorker($options, $share);
            $this->safeDeleteShare($share);
        }
    }

    private function deleteFileWorker($options, $share)
    {
        $file = FileServiceFunctionalTestData::getInterestingFileName($share);

        // Make sure there is something to test
        $dataSize = 512;
        $content = FileServiceFunctionalTestData::getRandomBytes($dataSize);
        $this->restProxy->createFileFromContent($share, $file, $content);

        try {
            if (null === $options) {
                $this->restProxy->deleteFile($share, $file);
            } else {
                $this->restProxy->deleteFile($share, $file, $options);
            }
            $deleted = true;

            if (null === $options) {
                $options = new FileServiceOptions();
            }

            if (null !== $options->getTimeout() && $options->getTimeout() < 1) {
                self::assertTrue(false, 'Expect negative timeouts in $options to throw');
            }

            $listDirectoriesAndFilesResult =
                $this->restProxy->listDirectoriesAndFiles($share);

            $files = $listDirectoriesAndFilesResult->getFiles();

            self::assertCount(0, $files, 'File should be deleted');
        } catch (ServiceException $e) {
            if (null !== $options->getTimeout() && $options->getTimeout() < 1) {
                self::assertEquals(
                    TestResources::STATUS_INTERNAL_SERVER_ERROR,
                    $e->getCode(),
                    'bad timeout: deleteHttpStatusCode'
                );
            } else {
                throw $e;
            }
        }
    }

    public function testCopyFileNoOptions()
    {
        $sourceShares = FileServiceFunctionalTestData::$testShareNames;

        $destShares = FileServiceFunctionalTestData::$testShareNames;

        foreach ($sourceShares as $sourceShare) {
            foreach ($destShares as $destShare) {
                $this->copyFileWorker(null, null, $sourceShare, $destShare);
            }
        }
    }

    public function testCopyFile()
    {
        $sourceShare = FileServiceFunctionalTestData::$testShareNames[0];
        $destShare = FileServiceFunctionalTestData::$testShareNames[1];

        $pairs = FileServiceFunctionalTestData::getCopyFileMetaOptionsPairs();
        foreach ($pairs as $pair) {
            $this->copyFileWorker(
                $pair['metadata'],
                $pair['options'],
                $sourceShare,
                $destShare
            );
        }
    }

    private function copyFileWorker($metadata, $options, $sourceShare, $destShare)
    {
        $sourceFile = FileServiceFunctionalTestData::getInterestingFileName();
        $destFile = FileServiceFunctionalTestData::getInterestingFileName();

        // Make sure there is something to test
        $sourceDataSize = 512;
        $content = FileServiceFunctionalTestData::getRandomBytes($sourceDataSize);
        $this->restProxy->createFileFromContent($sourceShare, $sourceFile, $content);

        $destDataSize = 2048;
        $this->restProxy->createFile($destShare, $destFile, $destDataSize);

        $sourceMeta = FileServiceFunctionalTestData::getNiceMetadata();
        $this->restProxy->setFileMetadata($sourceShare, $sourceFile, $sourceMeta);

        $sourcePath = sprintf(
            '%s%s/%s',
            (string) $this->restProxy->getPsrPrimaryUri(),
            $sourceShare,
            $sourceFile
        );

        try {
            if (null === $metadata) {
                $this->restProxy->copyFile(
                    $destShare,
                    $destFile,
                    $sourcePath,
                    [],
                    $options
                );
            } else {
                $this->restProxy->copyFile(
                    $destShare,
                    $destFile,
                    $sourcePath,
                    $metadata,
                    $options
                );
            }

            if (null === $options) {
                $options = new FileServiceOptions();
            }

            if (null !== $options->getTimeout() && $options->getTimeout() < 1) {
                self::assertTrue(false, 'Expect negative timeouts in $options to throw');
            }
            $listDirectoriesAndFilesResult =
                $this->restProxy->listDirectoriesAndFiles($destShare);
            $files = $listDirectoriesAndFilesResult->getFiles();

            $getFileResult = $this->restProxy->getFile($destShare, $destFile);

            $this->verifyCopyFileWorker(
                $sourceShare,
                $destShare,
                $options,
                $files,
                $getFileResult,
                $sourceDataSize,
                $metadata,
                $sourceMeta,
                $content
            );
        } catch (ServiceException $e) {
            if (null === $options) {
                $options = new CopyFileOptions();
            }

            if (null !== $options->getTimeout() && $options->getTimeout() < 1) {
                self::assertEquals(500, $e->getCode(), 'bad timeout: deleteHttpStatusCode');
            } else {
                throw $e;
            }
        }

        // Clean up.
        $this->restProxy->deleteFile($sourceShare, $sourceFile);
        $this->restProxy->deleteFile($destShare, $destFile);
    }

    private function verifyCopyFileWorker(
        $sourceShare,
        $destShare,
        $options,
        $files,
        $getFileResult,
        $sourceDataSize,
        $metadata,
        $sourceMeta,
        $sourceContent
    ) {
        self::assertEquals(
            $sourceShare == $destShare ? 2 : 1,
            count($files)
        );
        self::assertEquals(
            $sourceDataSize,
            $getFileResult->getProperties()->getContentLength(),
            'Dest length should be the same as the source length'
        );

        self::assertNotNull($getFileResult->getMetadata(), 'file Metadata');
        $expectedMetadata = $metadata == null ? $sourceMeta : $metadata;
        $resMetadata = $getFileResult->getMetadata();
        self::assertEquals(
            \count($expectedMetadata),
            \count($resMetadata),
            'Metadata'
        );
        foreach ($expectedMetadata as $key => $value) {
            self::assertEquals(
                $value,
                $resMetadata[\strtolower($key)],
                'Metadata(' . $key . ')'
            );
        }
        $resContent = \stream_get_contents($getFileResult->getContentStream());
        self::assertEquals($sourceContent, $resContent);

        // Make sure the last modified date is within 10 seconds
        $now = new \DateTime();
        self::assertTrue(
            FileServiceFunctionalTestData::diffInTotalSeconds(
                $getFileResult->getProperties()->getLastModified(),
                $now
            ) < 10,
            'Last modified date (' .
                $getFileResult->getProperties()->getLastModified()->format(
                    \DateTime::RFC1123
                ) . ')' . ' should be within 10 seconds of $now (' .
                $now->format(\DateTime::RFC1123) . ')'
        );
    }

    public function testPutListClearRanges()
    {
        $rangesArray = FileServiceFunctionalTestData::getRangesArray();
        $share = FileServiceFunctionalTestData::getInterestingShareName();
        $this->safeCreateShare($share);
        $file = FileServiceFunctionalTestData::getInterestingFileName();
        $this->restProxy->createFile($share, $file, 2048);
        foreach ($rangesArray as $array) {
            $this->putListClearRangesWorker(
                $share,
                $file,
                $array['putRange'],
                $array['clearRange'],
                $array['listRange'],
                $array['resultListRange']
            );
        }
        $this->safeDeleteShare($share);
    }

    public function testCreateFileWithNameIsZero()
    {
        $share = FileServiceFunctionalTestData::getInterestingShareName();
        $this->safeCreateShare($share);
        $file = '0';
        $this->restProxy->createFile($share, $file, 2048);
        $res = $this->restProxy->getFileProperties($share, $file);
        self::assertEquals(2048, $res->getContentLength());
        $this->safeDeleteShare($share);
    }

    private function putListClearRangesWorker(
        $share,
        $file,
        $putRange,
        $clearRange,
        $listRange,
        $resultListRange
    ) {
        if ($putRange != null) {
            $length = $putRange->getLength();
            if ($length == null) {
                $length = 2048 - $putRange->getStart();
            }
            $content = FileServiceFunctionalTestData::getRandomBytes($length);
            $options = new PutFileRangeOptions();
            //setting the wrong md5.
            $options->setContentMD5(Utilities::calculateContentMD5(''));
            $message = '';
            try {
                $this->restProxy->putFileRange($share, $file, $content, $putRange, $options);
            } catch (ServiceException $e) {
                $message = $e->getMessage();
            }
            self::assertContains('400', $message);
            self::assertContains(
                'The MD5 value specified in the request did not match with the MD5 value calculated by the server.',
                $message
            );
            //Ends debug code snippet
            // Now set the correct content MD5
            $options->setContentMD5(Utilities::calculateContentMD5($content));
            $this->restProxy->putFileRange($share, $file, $content, $putRange, $options);
            $getOptions = new GetFileOptions();
            $getOptions->setRange($putRange);
            $getOptions->setRangeGetContentMD5(true);
            $result = $this->restProxy->getFile($share, $file, $getOptions);
            $actualContent = stream_get_contents($result->getContentStream());
            $actualMD5 = $result->getProperties()->getRangeContentMD5();
            //Validate
            self::assertEquals(Utilities::calculateContentMD5($content), $actualMD5);
            self::assertEquals($content, $actualContent);
        }
        if ($clearRange != null) {
            $this->restProxy->clearFileRange($share, $file, $clearRange);
        }
        //Validate result
        $listResult = $this->restProxy->listFileRange($share, $file, $listRange);
        self::assertEquals(2048, $listResult->getContentLength());
        $resultRanges = $listResult->getRanges();
        for ($i = 0; $i < count($resultRanges); ++$i) {
            self::assertEquals($resultListRange[$i], $resultRanges[$i]);
        }
    }

    public function testDirectoriesLogic()
    {
        $commands = FileServiceFunctionalTestData::getDirectoriesAndFilesToCreateOrDelete();
        $share = FileServiceFunctionalTestData::getInterestingShareName();
        $this->safeCreateShare($share);
        foreach ($commands as $command) {
            $this->directoriesLogicWorker(
                $share,
                $command['operation'],
                $command['type'],
                $command['path'],
                $command['error']
            );
        }
        $this->safeDeleteShare($share);
    }

    private function directoriesLogicWorker(
        $share,
        $operation,
        $type,
        $path,
        $error
    ) {
        $worker = null;
        $proxy = $this->restProxy;
        if ($type == 'dir') {
            if ($operation == 'create') {
                $worker = static function () use ($share, $path, $proxy) {
                    $proxy->createDirectory($share, $path);
                };
            } elseif ($operation == 'delete') {
                $worker = static function () use ($share, $path, $proxy) {
                    $proxy->deleteDirectory($share, $path);
                };
            }
        } elseif ($type == 'file') {
            if ($operation == 'create') {
                $worker = static function () use ($share, $path, $proxy) {
                    $proxy->createFile($share, $path, 2048);
                };
            } elseif ($operation == 'delete') {
                $worker = static function () use ($share, $path, $proxy) {
                    $proxy->deleteFile($share, $path);
                };
            }
        }
        self::assertNotNull($worker);
        $message = '';
        try {
            $worker();
        } catch (ServiceException $e) {
            $message = $e->getMessage();
        }
        if ($error == '') {
            self::assertEquals($error, $message);
        } else {
            self::assertContains($error, $message);
        }
    }

    public function testSetDirectoryMetadataNoOptions()
    {
        $interestingMetadata = FileServiceFunctionalTestData::getInterestingMetadata();
        foreach ($interestingMetadata as $meta) {
            $share = FileServiceFunctionalTestData::getInterestingShareName();
            $this->safeCreateShare($share);
            $this->setDirectoryMetadataWorker($share, null, $meta);
            $this->safeDeleteShare($share);
        }
    }

    public function testSetDirectoryMetadata()
    {
        $interestingSetFileMetadataOptions = FileServiceFunctionalTestData::getFileServiceOptions();
        $interestingMetadata = FileServiceFunctionalTestData::getInterestingMetadata();

        foreach ($interestingSetFileMetadataOptions as $options) {
            foreach ($interestingMetadata as $meta) {
                $share = FileServiceFunctionalTestData::getInterestingShareName();
                $this->safeCreateShare($share);
                $this->setDirectoryMetadataWorker($share, $options, $meta);
                $this->safeDeleteShare($share);
            }
        }
    }

    private function setDirectoryMetadataWorker($share, $options, $metadata)
    {
        $dir = FileServiceFunctionalTestData::getInterestingDirectoryName();

        // Make sure there is something to test
        $this->restProxy->createDirectory($share, $dir);

        $firstkey = '';
        if (null !== $metadata && count($metadata) > 0) {
            $firstkey = array_keys($metadata);
            $firstkey = $firstkey[0];
        }

        try {
            // And put in some properties
            (null === $options ?
            $this->restProxy->setDirectoryMetadata($share, $dir, $metadata) :
            $this->restProxy->setDirectoryMetadata(
                $share,
                $dir,
                $metadata,
                $options
            ));
            if (null === $options) {
                $options = new FileServiceOptions();
            }

            if (null !== $options->getTimeout() && $options->getTimeout() < 1) {
                self::assertTrue(false, 'Expect negative timeouts in $options to throw');
            }
            if (Utilities::startsWith($firstkey, '<')) {
                self::assertTrue(false, 'Should get HTTP request error if the metadata is invalid');
            }

            $res = $this->restProxy->getDirectoryMetadata($share, $dir);
            $this->verifyGetDirectoryMetadataWorker($res, $metadata);
        } catch (ServiceException $e) {
            if (Utilities::startsWith($firstkey, '<')) {
                self::assertEquals(TestResources::STATUS_BAD_REQUEST, $e->getCode(), 'getCode');
            } elseif (null !== $options->getTimeout() && $options->getTimeout() < 1) {
                self::assertEquals(TestResources::STATUS_INTERNAL_SERVER_ERROR, $e->getCode(), 'bad timeout: getCode');
            } else {
                throw $e;
            }
        }
    }

    private function verifyGetDirectoryMetadataWorker($res)
    {
        self::assertNotNull($res->getETag(), 'directory getETag');
        self::assertNotNull($res->getLastModified(), 'directory getLastModified');

        // Make sure the last modified date is within 10 seconds
        $now = new \DateTime();
        self::assertTrue(
            FileServiceFunctionalTestData::diffInTotalSeconds(
                $res->getLastModified(),
                $now
            ) < 10000,
            'Last modified date (' .
            $res->getLastModified()->format(\DateTime::RFC1123) . ') ' .
                'should be within 10 seconds of $now (' .
                $now->format(\DateTime::RFC1123) . ')'
        );
    }

    public function testMiddlewares()
    {
        //setup middlewares.
        $historyMiddleware = new HistoryMiddleware();
        $retryMiddleware = RetryMiddlewareFactory::create(
            RetryMiddlewareFactory::GENERAL_RETRY_TYPE,
            3,
            1
        );

        //setup options for the first try.
        $options = new ListSharesOptions();
        $options->setMiddlewares([$historyMiddleware]);
        //get the response of the server.
        $result = $this->restProxy->listShares($options);
        $response = $historyMiddleware->getHistory()[0]['response'];
        $request = $historyMiddleware->getHistory()[0]['request'];

        //setup the mock handler
        $mock = MockHandler::createWithMiddleware([
            new RequestException(
                'mock 408 exception',
                $request,
                new Response(408, ['test_header' => 'test_header_value'])
            ),
            new Response(500, ['test_header' => 'test_header_value']),
            $response,
        ]);
        $restOptions = ['http' => ['handler' => $mock]];
        $mockProxy = FileRestProxy::createFileService($this->connectionString, $restOptions);

        //test using mock handler.
        $options = new ListSharesOptions();
        $options->setMiddlewares([$retryMiddleware, $historyMiddleware]);
        $newResult = $mockProxy->listShares($options);
        self::assertTrue(
            $result == $newResult,
            'Mock result does not match server behavior'
        );
        self::assertTrue(
            $historyMiddleware->getHistory()[1]['reason']->getMessage() == 'mock 408 exception',
            'Mock handler does not gave the first 408 exception correctly'
        );
        self::assertTrue(
            $historyMiddleware->getHistory()[2]['reason']->getCode() == 500,
            'Mock handler does not gave the second 500 response correctly'
        );
    }
}
