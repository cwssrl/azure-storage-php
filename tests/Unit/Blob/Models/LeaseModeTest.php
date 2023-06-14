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

use AzureOSS\Storage\Blob\Models\LeaseMode;

/**
 * Unit tests for class LeaseMode
 *
 * @see      https://github.com/azure/azure-storage-php
 */
class LeaseModeTest extends \PHPUnit\Framework\TestCase
{
    public function testLeaseMode()
    {
        self::assertEquals(LeaseMode::ACQUIRE_ACTION, 'acquire');
        self::assertEquals(LeaseMode::BREAK_ACTION, 'break');
        self::assertEquals(LeaseMode::RELEASE_ACTION, 'release');
        self::assertEquals(LeaseMode::RENEW_ACTION, 'renew');
    }
}
