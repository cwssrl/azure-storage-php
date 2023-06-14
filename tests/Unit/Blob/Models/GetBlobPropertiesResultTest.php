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

namespace MicrosoftAzure\Storage\Tests\Unit\Blob\Models;

use AzureOSS\Storage\Blob\Models\BlobProperties;
use AzureOSS\Storage\Blob\Models\GetBlobPropertiesResult;
use MicrosoftAzure\Storage\Tests\Framework\TestResources;

/**
 * Unit tests for class GetBlobPropertiesResult
 *
 * @see      https://github.com/azure/azure-storage-php
 */
class GetBlobPropertiesResultTest extends \PHPUnit\Framework\TestCase
{
    public function testCreate()
    {
        // Setup
        $sample = TestResources::listBlobsOneEntry();
        $expected = $sample['Blobs']['Blob']['Properties'];
        $expectedProperties = BlobProperties::createFromHttpHeaders($expected);
        $expected['x-ms-meta-'] = $sample['Blobs']['Blob']['Metadata'];

        // Test
        $actual = GetBlobPropertiesResult::create($expected);

        // Assert
        self::assertEquals($expectedProperties, $actual->getProperties());
        self::assertEquals(['' => $expected['x-ms-meta-']], $actual->getMetadata());
    }
}
