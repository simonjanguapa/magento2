<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Api\Test\Unit\ExtensionAttribute\Config;

use Magento\Framework\Api\ExtensionAttribute\Config\Converter;

class ConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Api\ExtensionAttribute\Config\Converter
     */
    protected $_converter;

    /**
     * Initialize parameters
     */
    protected function setUp()
    {
        $this->_converter = new \Magento\Framework\Api\ExtensionAttribute\Config\Converter();
    }

    /**
     * Test invalid data
     */
    public function testInvalidData()
    {
        $result = $this->_converter->convert(['invalid data']);
        $this->assertEmpty($result);
    }

    /**
     * Test empty data
     */
    public function testConvertNoElements()
    {
        $result = $this->_converter->convert(new \DOMDocument());
        $this->assertEmpty($result);
    }

    /**
     * Test converting valid data object config
     */
    public function testConvert()
    {
        $expected = [
            'Magento\Tax\Api\Data\TaxRateInterface' => [
            ],
            'Magento\Catalog\Api\Data\ProductInterface' => [
                'stock_item' => [
                    Converter::DATA_TYPE => 'Magento\CatalogInventory\Api\Data\StockItemInterface',
                    Converter::RESOURCE_PERMISSIONS => [],
                    Converter::JOIN_DIRECTIVE => null,
                ],
            ],
            'Magento\Customer\Api\Data\CustomerInterface' => [
                'custom_1' => [
                    Converter::DATA_TYPE => 'Magento\Customer\Api\Data\CustomerCustom',
                    Converter::RESOURCE_PERMISSIONS => [],
                    Converter::JOIN_DIRECTIVE => null,
                ],
                'custom_2' => [
                    Converter::DATA_TYPE => 'Magento\CustomerExtra\Api\Data\CustomerCustom2',
                    Converter::RESOURCE_PERMISSIONS => [],
                    Converter::JOIN_DIRECTIVE => null,
                ],
            ],
            'Magento\Customer\Api\Data\CustomerInterface2' => [
                'custom_with_permission' => [
                    Converter::DATA_TYPE => 'Magento\Customer\Api\Data\CustomerCustom',
                    Converter::RESOURCE_PERMISSIONS => [
                        'Magento_Customer::manage',
                    ],
                    Converter::JOIN_DIRECTIVE => null,
                ],
                'custom_with_multiple_permissions' => [
                    Converter::DATA_TYPE => 'Magento\CustomerExtra\Api\Data\CustomerCustom2',
                    Converter::RESOURCE_PERMISSIONS => [
                        'Magento_Customer::manage',
                        'Magento_Customer::manage2',
                    ],
                    Converter::JOIN_DIRECTIVE => null,
                ],
            ],
        ];

        $xmlFile = __DIR__ . '/_files/extension_attributes.xml';
        $dom = new \DOMDocument();
        $dom->loadXML(file_get_contents($xmlFile));
        $result = $this->_converter->convert($dom);
        $this->assertEquals($expected, $result);
    }

    /**
     * Test converting valid data object config
     */
    public function testConvertWithJoinDirectives()
    {
        $expected = [
            'Magento\Customer\Api\Data\CustomerInterface' => [
                'library_card_id' => [
                    Converter::DATA_TYPE => 'string',
                    Converter::RESOURCE_PERMISSIONS => [],
                    Converter::JOIN_DIRECTIVE => [
                        Converter::JOIN_REFERENCE_TABLE => "library_account",
                        Converter::JOIN_SELECT_FIELDS => [
                            [
                                Converter::JOIN_SELECT_FIELD => "library_card_id",
                                Converter::JOIN_SELECT_FIELD_SETTER => ""
                            ]
                        ],
                        Converter::JOIN_JOIN_ON_FIELD => "id",
                        Converter::JOIN_REFERENCE_FIELD => "customer_id",
                    ],
                ],
                'reviews' => [
                    Converter::DATA_TYPE => 'Magento\Reviews\Api\Data\Reviews[]',
                    Converter::RESOURCE_PERMISSIONS => [],
                    Converter::JOIN_DIRECTIVE => [
                        Converter::JOIN_REFERENCE_TABLE => "reviews",
                        Converter::JOIN_SELECT_FIELDS => [
                            [
                                Converter::JOIN_SELECT_FIELD => "comment",
                                Converter::JOIN_SELECT_FIELD_SETTER => ""
                            ],
                            [
                                Converter::JOIN_SELECT_FIELD => "rating",
                                Converter::JOIN_SELECT_FIELD_SETTER => ""
                            ]
                        ],
                        Converter::JOIN_JOIN_ON_FIELD => "customer_id",
                        Converter::JOIN_REFERENCE_FIELD => "customer_id",
                    ],
                ],
            ],
        ];

        $xmlFile = __DIR__ . '/_files/extension_attributes_with_join_directives.xml';
        $dom = new \DOMDocument();
        $dom->loadXML(file_get_contents($xmlFile));
        $result = $this->_converter->convert($dom);
        $this->assertEquals($expected, $result);
    }
}
