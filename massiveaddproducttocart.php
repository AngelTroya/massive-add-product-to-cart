<?php
/**
 *  ModuleModuleMassiveAddProductToCart For Help & Support angelmaria87@gmail.com
 *
 *  @author    Ángel María de Troya de la Vega
 *  @copyright 2014
 */

if (!defined('_PS_VERSION_'))
		exit;

class massiveaddproducttocart extends Module
{

	public function __construct()
	{
		$this->name = 'massiveaddproducttocart';
		$this->tab = 'front_office_features';
		$this->version = 1.0;
		$this->author = 'Selectomer TIC';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Massive add product to cart');
		$this->description = $this->l('Selectommerce Module');

		$path = dirname(__FILE__);
		if (strpos(__FILE__, 'Module.php') !== false){
			$path .= '/../modules/'.$this->name;
		}

		// include_once $path.'/OntologiesProduct.php';
	}

public function install() {
        $res = TRUE;

        if (!parent::install() OR ! $this->registerHook('displayHeader') OR ! $this->registerHook('displayLeftColumn') OR ! $res) {

            return FALSE;
        }

        return $res;
    }

    public function uninstall() {
        $res = Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'ontologies_product`');

        if (!$res || !parent::uninstall() OR ! $this->unregisterHook('displayHeader') OR ! $this->registerHook('displayLeftColumn')
        ) {
            return $res = FALSE;
        }

        return $res;
    }

    public function hookDisplayHeader()
	{
		$this->context->controller->addCSS(($this->_path).'css/massiveaddproducttocart.css', 'all');
	}

    public function hookdisplayLeftColumn($params) {

        return $this->display(__FILE__, 'views/templates/hook/importproductstocart.tpl');
    }

	public function hookDisplayCartEmpty($params) {

        return $this->display(__FILE__, 'views/templates/hook/importproductstocart.tpl');
    }
}

