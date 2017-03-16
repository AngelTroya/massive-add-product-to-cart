<?php
/**
 *  ModuleModuleMassiveAddProductToCart For Help & Support angelmaria87@gmail.com
 *
 *  @author    Ángel María de Troya de la Vega
 *  @copyright 2014
 */

//session_start();

class massiveaddproducttocartsteptwoModuleFrontController extends ModuleFrontController {

    protected $ids;

    protected static $_products;

    public function init() {

        parent::init();

        $context = Context::getContext();

//        $dataset=$this->context->cookie->dataset;
//        $dataset = $_SESSION['dataset'];
        //p($dataset);

        $references = array();
        $barras = array();
        $cantidades = array();

        $importCount = 0;

        while(Tools::getIsset("csvImport$importCount")){
            $importProduct =  Tools::jsonDecode(Tools::getValue("csvImport$importCount"));

            $select_count = 0;
//        foreach ($dataset as $data) {
//            recorro todos los selects
            while (Tools::getIsset("select$select_count")) {
                //si en el select elegimos la referencia
                $asignation = Tools::getValue("select$select_count");

                if ($asignation == 0) {

                    $references[] = $importProduct[$select_count];
                } elseif ($asignation == 1) {
                   //si en el select elegimos el código de barras

                    $barras[] = $importProduct[$select_count];
                } elseif ($asignation == 2) {

                    //si en el select elegimos la cantidad
                    $cantidades[] = $importProduct[$select_count];
                }
                $select_count++;
            }
            $importCount++;
        }

        //construyo un array multidimensional con todos los valores de la compra
        $buy=array();
        for ($i = 0; $i < count($references); $i++) {
            $buy[$i]['reference'] = $references[$i];
            $buy[$i]['ean13'] = $barras[$i];
            $buy[$i]['amount'] = $cantidades[$i];
        }



        $query = new DbQuery();
        $query->select('p.id_product, p.reference, p.ean13, sa.quantity, sa.id_product_attribute, p.available_for_order');
        $query->from('product', 'p');
        $query->leftjoin('stock_available', 'sa', 'p.id_product = sa.id_product');

        //$catalog = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        $product = new Product();
        //por cada producto de la lista de la compra
        for ($i = 0; $i < count($buy); $i++) {
            $exists = false;
            //si su referencia existe la buscamos en la BD

            if ($buy[$i]['reference'] != '') {
                $exists = $product->existsRefInDatabase($buy[$i]['reference']);
                if ($exists) {
                    $query = new DbQuery();
                    $query->select('p.id_product');
                    $query->from('product', 'p');
                    $query->where('p.reference = \'' . pSQL($references[$i]) . '\'');

                    $a = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);

                    $buy[$i]['id'] = $a;
                }
            }
            if (!$exists && $buy[$i]['ean13'] != '') {
                $exists = $product->getIdByEan13($buy[$i]['ean13']);
                if ($exists) {
                    $buy[$i]['id'] = $exists;
                }
            }
            //si llego aqui y existe podemos añadir al carrito el producto
            if ($exists) {

                $mode = (Tools::getIsset('update') && $buy[$i]['id']) ? 'update' : 'add';
                if ($buy[$i]['amount'] == 0) {

                    $this->errors[] = Tools::displayError('Product ' . $buy[$i]['reference'] . ' Null quantity. (' . $buy[$i]['amount'] . ')');
                } elseif (!$buy[$i]['id']) {
                    $this->errors[] = Tools::displayError('Product ' . $buy[$i]['reference'] . ' not found');
                }
                $product = new Product($buy[$i]['id'], true, $this->context->language->id);
                if (!$product->id || !$product->active) {
                    $this->errors[] = Tools::displayError('The product ' . $buy[$i]['reference'] . ' is no longer available.', false);
                }
                //$qty_to_check = $buy[$i]['amount'];

                //añado al carrito si hemos llegado aquí
                if (!$this->errors && $mode == 'add') {
                    // Add cart if no cart found
                    if (!$this->context->cart->id) {
                        if (Context::getContext()->cookie->id_guest) {
                            $guest = new Guest(Context::getContext()->cookie->id_guest);
                            $this->context->cart->mobile_theme = $guest->mobile_theme;
                        }
                        $this->context->cart->add();
                        if ($this->context->cart->id) {
                            $this->context->cookie->id_cart = (int) $this->context->cart->id;
                        }
                    }



                    $cart_rules = $this->context->cart->getCartRules();
                    //$update_quantity = $this->updateQuantity($buy[$i]['amount'], $buy[$i]['id']);
                    $update_quantity = $this->context->cart->updateQty($buy[$i]['amount'], $buy[$i]['id']);
                    if ($update_quantity < 0) {
                        // If product has attribute, minimal quantity is set with minimal quantity of attribute
                        $minimal_quantity = ($buy[$i]['id']) ? Attribute::getAttributeMinimalQty($buy[$i]['id']) : $product->minimal_quantity;
                        $this->errors[] = sprintf(Tools::displayError('You must add %d minimum quantity', false), $minimal_quantity);
                    } else
                    if (!$update_quantity) {
                        $this->errors[] = Tools::displayError('You already have the maximum quantity available for this product.', false);
                    } elseif ((int) Tools::getValue('allow_refresh')) {
                        // If the cart rules has changed, we need to refresh the whole cart
                        $cart_rules2 = $this->context->cart->getCartRules();
                        if (count($cart_rules2) != count($cart_rules)) {
                            $this->ajax_refresh = true;
                        } else {
                            $rule_list = array();
                            foreach ($cart_rules2 as $rule) {
                                $rule_list[] = $rule['id_cart_rule'];
                            }
                            foreach ($cart_rules as $rule) {
                                if (!in_array($rule['id_cart_rule'], $rule_list)) {
                                    $this->ajax_refresh = true;
                                    break;
                                }
                            }
                        }
                    }
                }
                //no hago un if por errors porque dejaría de añadir los productos siguientes
                //p($this->errors);
                $removed = CartRule::autoRemoveFromCart();
                CartRule::autoAddToCart();
                if (count($removed) && (int) Tools::getValue('allow_refresh')) {
                    $this->ajax_refresh = true;
                }
            }
        }
        //Tools::redirect('Location: index.php');
    }
}
