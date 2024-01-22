<?php

namespace AzureOSS\Storage\File\Models;

use AzureOSS\Storage\Common\Internal\Utilities;
use AzureOSS\Storage\Common\Internal\Validate;
use AzureOSS\Storage\File\Internal\FileResources as Resources;

class FileProperties
{
    private $lastModified;
    private $contentLength;
    private $contentType;
    private $etag;
    private $contentMD5;
    private $contentEncoding;
    private $contentLanguage;
    private $cacheControl;
    private $contentDisposition;
    private $contentRange;
    private $copyCompletionTime;
    private $copyStatusDescription;
    private $copyID;
    private $copyProgress;
    private $copySource;
    private $copyStatus;
    private $rangeContentMD5;

    /**
     * Creates FileProperties object from $parsed response in array
     * representation of http headers
     *
     * @param array $parsed parsed response in array format.
     *
     * @internal
     *
     * @return FileProperties
     */
    public static function createFromHttpHeaders(array $parsed)
    {
        $result = new FileProperties();
        $clean = array_change_key_case($parsed);

        $lastModified = Utilities::tryGetValue($parsed, Resources::LAST_MODIFIED);

        $result->setLastModified(
            Utilities::rfc1123ToDateTime($lastModified)
        );

        $result->setContentLength(
            (int) Utilities::tryGetValue($parsed, Resources::CONTENT_LENGTH)
        );

        $result->setContentType(
            Utilities::tryGetValue($parsed, Resources::CONTENT_TYPE_LOWER_CASE)
        );

        $result->setETag(
            Utilities::tryGetValue($parsed, Resources::ETAG)
        );

        if (
            Utilities::tryGetValue($parsed, Resources::CONTENT_MD5)
            && !Utilities::tryGetValue($parsed, Resources::CONTENT_RANGE)
        ) {
            $result->setContentMD5(
                Utilities::tryGetValue($parsed, Resources::CONTENT_MD5)
            );
        } else {
            $result->setContentMD5(
                Utilities::tryGetValue($parsed, Resources::FILE_CONTENT_MD5)
            );
            $result->setRangeContentMD5(
                Utilities::tryGetValue($parsed, Resources::CONTENT_MD5)
            );
        }

        $result->setContentEncoding(
            Utilities::tryGetValue($parsed, Resources::CONTENT_ENCODING)
        );

        $result->setContentLanguage(
            Utilities::tryGetValue($parsed, Resources::CONTENT_LANGUAGE)
        );

        $result->setCacheControl(
            Utilities::tryGetValue($parsed, Resources::CACHE_CONTROL)
        );

        $result->setContentDisposition(
            Utilities::tryGetValue($parsed, Resources::CONTENT_DISPOSITION)
        );

        $result->setContentRange(
            Utilities::tryGetValue($parsed, Resources::CONTENT_RANGE)
        );

        $result->setCopyCompletionTime(
            Utilities::tryGetValue($parsed, Resources::X_MS_COPY_COMPLETION_TIME)
        );

        $result->setCopyStatusDescription(
            Utilities::tryGetValue($parsed, Resources::X_MS_COPY_STATUS_DESCRIPTION)
        );

        $result->setCopyID(
            Utilities::tryGetValue($parsed, Resources::X_MS_COPY_ID)
        );

        $result->setCopyProgress(
            Utilities::tryGetValue($parsed, Resources::X_MS_COPY_PROGRESS)
        );

        $result->setCopySource(
            Utilities::tryGetValue($parsed, Resources::X_MS_COPY_SOURCE)
        );

        $result->setCopyStatus(
            Utilities::tryGetValue($parsed, Resources::X_MS_COPY_STATUS)
        );

        return $result;
    }

    /**
     * Gets file lastModified.
     *
     * @return \DateTime
     */
    public function getLastModified()
    {
        return $this->lastModified;
    }

    /**
     * Sets file lastModified.
     *
     * @param \DateTime $lastModified value.
     */
    protected function setLastModified(\DateTime $lastModified)
    {
        Validate::isDate($lastModified);
        $this->lastModified = $lastModified;
    }

    /**
     * Gets file etag.
     *
     * @return string
     */
    public function getETag()
    {
        return $this->etag;
    }

    /**
     * Sets file etag.
     *
     * @param string $etag value.
     */
    protected function setETag($etag)
    {
        $this->etag = $etag;
    }

    /**
     * Gets file contentType.
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Sets file contentType.
     *
     * @param string $contentType value.
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * Gets file contentRange.
     *
     * @return string
     */
    public function getContentRange()
    {
        return $this->contentRange;
    }

    /**
     * Sets file contentRange.
     *
     * @param string $contentRange value.
     */
    protected function setContentRange($contentRange)
    {
        $this->contentRange = $contentRange;
    }

    /**
     * Gets file contentLength.
     *
     * @return int
     */
    public function getContentLength()
    {
        return $this->contentLength;
    }

    /**
     * Sets file contentLength.
     *
     * @param int $contentLength value.
     */
    public function setContentLength($contentLength)
    {
        Validate::isInteger($contentLength, 'contentLength');
        $this->contentLength = (int) $contentLength;
    }

    /**
     * Gets file contentEncoding.
     *
     * @return string
     */
    public function getContentEncoding()
    {
        return $this->contentEncoding;
    }

    /**
     * Sets file contentEncoding.
     *
     * @param string $contentEncoding value.
     */
    public function setContentEncoding($contentEncoding)
    {
        $this->contentEncoding = $contentEncoding;
    }

    /**
     * Gets file contentLanguage.
     *
     * @return string
     */
    public function getContentLanguage()
    {
        return $this->contentLanguage;
    }

    /**
     * Sets file contentLanguage.
     *
     * @param string $contentLanguage value.
     */
    public function setContentLanguage($contentLanguage)
    {
        $this->contentLanguage = $contentLanguage;
    }

    /**
     * Gets file contentMD5.
     *
     * @return string
     */
    public function getContentMD5()
    {
        return $this->contentMD5;
    }

    /**
     * Sets file contentMD5.
     *
     * @param string $contentMD5 value.
     */
    public function setContentMD5($contentMD5)
    {
        $this->contentMD5 = $contentMD5;
    }

    /**
     * Gets file range contentMD5.
     *
     * @return string
     */
    public function getRangeContentMD5()
    {
        return $this->rangeContentMD5;
    }

    /**
     * Sets file range contentMD5.
     *
     * @param string rangeContentMD5 value.
     */
    public function setRangeContentMD5($rangeContentMD5)
    {
        $this->rangeContentMD5 = $rangeContentMD5;
    }

    /**
     * Gets file cacheControl.
     *
     * @return string
     */
    public function getCacheControl()
    {
        return $this->cacheControl;
    }

    /**
     * Sets file cacheControl.
     *
     * @param string $cacheControl value.
     */
    public function setCacheControl($cacheControl)
    {
        $this->cacheControl = $cacheControl;
    }

    /**
     * Gets file contentDisposition.
     *
     * @return string
     */
    public function getContentDisposition()
    {
        return $this->contentDisposition;
    }

    /**
     * Sets file contentDisposition.
     *
     * @param string $contentDisposition value.
     */
    public function setContentDisposition($contentDisposition)
    {
        $this->contentDisposition = $contentDisposition;
    }

    /**
     * Gets file copyCompletionTime.
     *
     * @return string
     */
    public function getCopyCompletionTime()
    {
        return $this->copyCompletionTime;
    }

    /**
     * Sets file copyCompletionTime.
     *
     * @param string $copyCompletionTime value.
     */
    protected function setCopyCompletionTime($copyCompletionTime)
    {
        $this->copyCompletionTime = $copyCompletionTime;
    }

    /**
     * Gets file copyStatusDescription.
     *
     * @return string
     */
    public function getCopyStatusDescription()
    {
        return $this->copyStatusDescription;
    }

    /**
     * Sets file copyStatusDescription.
     *
     * @param string $copyStatusDescription value.
     */
    protected function setCopyStatusDescription($copyStatusDescription)
    {
        $this->copyStatusDescription = $copyStatusDescription;
    }

    /**
     * Gets file lease state.
     *
     * @return string
     */
    public function getCopyID()
    {
        return $this->copyID;
    }

    /**
     * Sets file lease state.
     *
     * @param string $copyID value.
     */
    protected function setCopyID($copyID)
    {
        $this->copyID = $copyID;
    }

    /**
     * Gets file lease duration.
     *
     * @return string
     */
    public function getCopyProgress()
    {
        return $this->copyProgress;
    }

    /**
     * Sets file copyStatusDescription.
     *
     * @param string $copyProgress value.
     */
    protected function setCopyProgress($copyProgress)
    {
        $this->copyProgress = $copyProgress;
    }

    /**
     * Gets file copySource.
     *
     * @return int
     */
    public function getCopySource()
    {
        return $this->copySource;
    }

    /**
     * Sets file copySource.
     *
     * @param string $copySource value.
     */
    protected function setCopySource($copySource)
    {
        $this->copySource = $copySource;
    }

    /**
     * Gets copy state of the file.
     *
     * @return CopyStatus
     */
    public function getCopyStatus()
    {
        return $this->copyStatus;
    }

    /**
     * Sets the copy state of the file.
     *
     * @param CopyStatus $copyStatus the copy state of the file.
     */
    protected function setCopyStatus($copyStatus)
    {
        $this->copyStatus = $copyStatus;
    }
}
