<?php

namespace PrestaShop\Module\TagConciergeFree\Controller;

use Exception;
use ModuleFrontController;
use RuntimeException;
use Throwable;
use Tools;

class AjaxController extends ModuleFrontController
{
    public function postProcess()
    {
        $response = [];
        try {
            switch (Tools::getValue('action')) {
                case 'getCart':
                    $response = $this->getCart();
                    break;
                default:
                    throw new RuntimeException('Unsupported action.');
            }
        } catch (Throwable $e) {
            // TODO log
        }

        ob_end_clean();
        header('Content-Type: application/json');
        $this->ajaxRender(json_encode($response));
    }

    /**
     * @throws Exception
     */
    private function getCart(): array
    {
        return $this->cart_presenter->present($this->context->cart);
    }
}
