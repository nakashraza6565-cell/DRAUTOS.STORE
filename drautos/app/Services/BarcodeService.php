<?php

namespace App\Services;

use Picqer\Barcode\BarcodeGeneratorPNG;
use Picqer\Barcode\BarcodeGeneratorHTML;

class BarcodeService
{
    protected $generator;
    protected $htmlGenerator;

    public function __construct()
    {
        $this->generator = new BarcodeGeneratorPNG();
        $this->htmlGenerator = new BarcodeGeneratorHTML();
    }

    /**
     * Generate barcode PNG image
     *
     * @param string $code
     * @param int $widthFactor
     * @param int $height
     * @return string Base64 encoded image
     */
    public function generatePNG($code, $widthFactor = 2, $height = 50)
    {
        $barcode = $this->generator->getBarcode($code, $this->generator::TYPE_CODE_128, $widthFactor, $height);
        return 'data:image/png;base64,' . base64_encode($barcode);
    }

    /**
     * Generate barcode HTML
     *
     * @param string $code
     * @return string
     */
    public function generateHTML($code)
    {
        return $this->htmlGenerator->getBarcode($code, $this->htmlGenerator::TYPE_CODE_128);
    }

    /**
     * Generate barcode and save to file
     *
     * @param string $code
     * @param string $path
     * @return bool
     */
    public function saveBarcode($code, $path)
    {
        $barcode = $this->generator->getBarcode($code, $this->generator::TYPE_CODE_128);
        return file_put_contents($path, $barcode) !== false;
    }

    /**
     * Generate product barcode based on SKU or ID
     *
     * @param mixed $product
     * @return string
     */
    public function generateProductBarcode($product)
    {
        $code = $product->barcode ?? $product->sku ?? 'PRD' . str_pad($product->id, 8, '0', STR_PAD_LEFT);
        return $this->generatePNG($code);
    }

    /**
     * Get barcode code for product
     *
     * @param mixed $product
     * @return string
     */
    public function getProductBarcodeCode($product)
    {
        return $product->barcode ?? $product->sku ?? 'PRD' . str_pad($product->id, 8, '0', STR_PAD_LEFT);
    }
}
