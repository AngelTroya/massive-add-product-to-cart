{**
 *  ModuleModuleMassiveAddProductToCart For Help & Support angelmaria87@gmail.com
 *
 *  @author    Ángel María de Troya de la Vega
 *  @copyright 2014
 **}
<form action="{$link->getModuleLink('massiveaddproducttocart', "massive")|escape:"htmlall"}" method="post" enctype="multipart/form-data" rel="form">
    <div id="import_csv" class="panel_massiveadd panel-default_massiveadd">
	<h3 class="panel-heading_massiveadd">{l s='Importa un CSV' mod='massiveaddproducttocart'} </h3>
	<div class="panel-body_massiveadd">
            <textarea name="import" rows="4" cols="20" class="ta_massiveadd">{l s='Importa aquí tu CSV a través del portapapeles' mod='massiveaddproducttocart'}</textarea><br>
            <br>
            <input type=file name="csvFileimport" class="bu_massiveadd" ><br><br>
            <label for="firstrow">
                <input type="checkbox" name="firstrow" value="1" >{l s='Marca si la primera línea son los títulos de los campos' mod='massiveaddproducttocart'}<br><br>
            </label>

            <input type="submit" value ="{l s='Import' mod='massiveaddproducttocart'} " target="_blank" class="button">
        </div>

    </div>
</form>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               