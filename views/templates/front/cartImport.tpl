{**
 *  ModuleModuleMassiveAddProductToCart For Help & Support angelmaria87@gmail.com
 *
 *  @author    Ángel María de Troya de la Vega
 *  @copyright 2014
 **}
<form action = "{$link->getModuleLink('massiveaddproducttocart', 'steptwo')|escape:"htmlall"}" method="post" enctype="multipart/form-data" rele = "form">
    <div class="panel-body" style="font-weight: bold;">
    {l s='Por favor, indíquenos las columnas correspondientes al código de barras, referencia y cantidad para poder importar su carrito correctamente y a continuación dele al botón de importar situado al pie de página.' mod='massiveaddproducttocart'}
    </div>
    <table {*style="font-size: smaller"*} class="table_massiveadd">
        <col style="width: 100px" span="3" />
            <tr>

                {for $num=0 to $datasetlength-1}
                    <td><select name = {"select"|cat:$num} width = "900" class="se_massiveadd"> <option value=0> {l s='Referencia' mod='massiveaddproducttocart'} </option> <option value=1 > {l s='Código de barras' mod='massiveaddproducttocart'} </option> <option value=2 > {l s='Cantidad' mod='massiveaddproducttocart'} </option> <option value=3 selected> {l s='Ignorar' mod='massiveaddproducttocart'} </option></select></td>
                {/for}
            </tr>

            {foreach from=$dataset key=key item=data}

                <tr>
                    {foreach from=$data item=field}
                        {assign var="fieldlength" value=$field.length}
                        {if $fieldlength gt 20}
                            {$field|truncate:20}
                            <td width="1500" >{$field|cat:"..."}</td>
                        {else}
                            <td width="1500" >{$field|escape:"htmlall"}</td>
                        {/if}

                    {/foreach}
                    <input name="csvImport{$key}" value='{$data|@json_encode}' hidden="hidden">
                </tr>
            {/foreach}
    </table>
    <input type="submit"  value={l s='Importar al carrito' mod='massiveaddproducttocart'}; target="_blank" class = "button">
</form>