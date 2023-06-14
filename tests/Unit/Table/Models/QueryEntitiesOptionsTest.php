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

namespace MicrosoftAzure\Storage\Tests\Unit\Table\Models;

use AzureOSS\Storage\Table\Models\EdmType;
use AzureOSS\Storage\Table\Models\Filters\Filter;
use AzureOSS\Storage\Table\Models\Query;
use AzureOSS\Storage\Table\Models\QueryEntitiesOptions;

/**
 * Unit tests for class QueryEntitiesOptions
 *
 * @see      https://github.com/azure/azure-storage-php
 */
class QueryEntitiesOptionsTest extends \PHPUnit\Framework\TestCase
{
    public function testSetQuery()
    {
        // Setup
        $options = new QueryEntitiesOptions();
        $expected = new Query();

        // Test
        $options->setQuery($expected);

        // Assert
        self::assertEquals($expected, $options->getQuery());
    }

    public function testSetNextPartitionKey()
    {
        // Setup
        $options = new QueryEntitiesOptions();
        $expected = 'parition';

        // Test
        $options->setNextPartitionKey($expected);

        // Assert
        self::assertEquals($expected, $options->getNextPartitionKey());
    }

    public function testSetNextRowKey()
    {
        // Setup
        $options = new QueryEntitiesOptions();
        $expected = 'edelo';

        // Test
        $options->setNextRowKey($expected);

        // Assert
        self::assertEquals($expected, $options->getNextRowKey());
    }

    public function testSetSelectFields()
    {
        // Setup
        $options = new QueryEntitiesOptions();
        $expected = ['customerId', 'customerName'];

        // Test
        $options->setSelectFields($expected);

        // Assert
        self::assertEquals($expected, $options->getSelectFields());
    }

    public function testSetTop()
    {
        // Setup
        $options = new QueryEntitiesOptions();
        $expected = 123;

        // Test
        $options->setTop($expected);

        // Assert
        self::assertEquals($expected, $options->getTop());
    }

    public function testSetFilter()
    {
        // Setup
        $options = new QueryEntitiesOptions();
        $expected = Filter::applyConstant('constValue', EdmType::STRING);

        // Test
        $options->setFilter($expected);

        // Assert
        self::assertEquals($expected, $options->getFilter());
    }

    public function testAddSelectField()
    {
        // Setup
        $options = new QueryEntitiesOptions();
        $field = 'customerId';
        $expected = [$field];

        // Test
        $options->addSelectField($field);

        // Assert
        self::assertEquals($expected, $options->getSelectFields());
    }
}
