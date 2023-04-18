<?php

namespace PrestaShop\Module\TagConciergeFree\Controller;

class AjaxController extends \ModuleFrontController
{
    public function postProcess()
    {
        $response = [];
        try {
            switch (\Tools::getValue('action')) {
                case 'getCart':
                    $response = $this->getCart();
                    break;
                default:
                    throw new \RuntimeException('Unsupported action.');
            }
        } catch (\Throwable $e) {
            // TODO log
        }

        ob_end_clean();
        header('Content-Type: application/json');
        $this->ajaxDie(json_encode($response, JSON_THROW_ON_ERROR, 512));
    }

    private function getCart(): array
    {
        return $this->cart_presenter->present($this->context->cart);
    }
}
