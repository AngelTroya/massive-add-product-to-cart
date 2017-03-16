<?php
/**
 *  ModuleModuleMassiveAddProductToCart For Help & Support angelmaria87@gmail.com
 *
 *  @author    Ángel María de Troya de la Vega
 *  @copyright 2014
 */

error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
class massiveaddproducttocartmassiveModuleFrontController extends ModuleFrontController {

    public function init() {
        parent::init();

    }

    public function initContent() {
        parent::initContent();

        $this->setTemplate('cartImport.tpl');

        //session_start();

        //textarea deberá llamarse el campo en el html del cual obtendremos el valor de lo capturado en el portapapeles

        $textarea = Tools::getValue('import');


        if (!Tools::getIsset('firstrow')) {
            $firstrow = Tools::getValue('firstrow');

        }

        if (!isset($_FILES["csvFileImport"])) {
            $file = $_FILES["csvFileimport"];
        }
        $csv_source = Tools::file_get_contents(($file[tmp_name]));


        if (!empty($csv_source)) {
            /* Normalizar EOL */
            $text = str_replace("\r\n", "\n", $csv_source);

            $text = str_replace("\r", "\n", $text);

            /* Convertir en tabla */

            $entries = explode("\n", $text);
        } elseif (!empty($textarea)) {
            $entries = explode("\n", $textarea);
        }
        else{
            //die("No ha introducido ningún archivo ni nada por el portapapeles");
             Tools::redirect("Location:".$_SERVER['HTTP_REFERER']);
        }
        $columnCount = 0;
        $cellDelimiter = null;
        $dataset = array();

      //vamos a intentar contar todas las columnas de la tabla
        foreach ($entries as $entry) {

            /* Definir tabla si la primera línea son los nombres de los campos */
            if ($firstrow == 1 && !empty($entry)) {

                if (!$columnCount) {
                    //$cells = preg_split('/[,;]/', $entry);
                    $matches = array();
                    //encuentra todas las comas y separa el texto de $entry según ellas y lo almacena en $matchs
                    if (preg_match_all('/(,)/', $entry, $matches)) {
                      if (count($matches[1]) > 1) {
                      $cellDelimiter = ',';
                      $columnCount = count($matches[1]) + 1;
                      }
                      //cambiamos el delimitador por un ;
                      } elseif (preg_match_all('/(;)/', $entry, $matches)) {

                        if (count($matches[1]) > 1) {
                            $cellDelimiter = ';';
                            //numero de campos = numero de ; +1
                            $columnCount = count($matches[1]) + 1;
                        }
                        //cambiamos el delimitador por un tabulador
                    } else if (preg_match_all('/(\t)/', $entry, $matches)) {
                        if (count($matches[1]) > 1) {
                            $cellDelimiter = "\t";
                            $columnCount = count($matches[1]) + 1;
                        }
                    } else {
                        p("Ni idea...");
                    }

                    //si ya tenemos contadas las columnas de la tabla y no son 0
                } elseif ($entry) {
                    //divide $entry según el delimitador hallado con anterioridad y lo guarda en un array
                    $rawCells = explode($cellDelimiter, $entry);
                    $processedCells = array();

                    $currentCellContent = '';
                    //recorro cada entrada guardada en $rawCells


                    for ($i = 0; $i < count($rawCells); $i++) {
                        $match = array();
                        if (!$currentCellContent && preg_match('/^(\d+)$/', $rawCells[$i], $match)) {
                            $processedCells[] = $match[1];
                        } elseif (!$currentCellContent && preg_match('/^([^"].*[^"])$/', $rawCells[$i], $match)) {
                            $processedCells[] = str_replace('""', '"', $match[1]);
                        } elseif (!$currentCellContent && preg_match('/^"([^"].*[^"])"$/', $rawCells[$i], $match)) {
                            $processedCells[] = str_replace('""', '"', $match[1]);
                        } elseif (!$currentCellContent && preg_match('/^"([^"])"$/', $rawCells[$i], $match)) {
                            $processedCells[] = $match[1];
                        } elseif (!$currentCellContent && preg_match('/^([^"])$/', $rawCells[$i], $match)) {
                            $processedCells[] = $match[1];
                        } elseif (preg_match('/^([^"].+[^"]|[^"]+)"$/', $rawCells[$i], $match)) {
                            $processedCells[] = str_replace('""', '"', $currentCellContent . $cellDelimiter . $match[1]);
                            $currentCellContent = '';
                        } elseif (preg_match('/^"([^"].+[^"]|[^"]+)$/', $rawCells[$i], $match)) {
                            $currentCellContent = $match[1];
                        } elseif (preg_match('/^([^"].+[^"]|[^"]+)$/', $rawCells[$i], $match)) {
                            $currentCellContent .= $cellDelimiter . $match[1];
                        } elseif (preg_match('/^$/', $rawCells[$i])) {
                            $processedCells[] = '';
                        } elseif (preg_match('//', $cellDelimiter, $match)) {

                        } else {
                            print_r($rawCells);
                            echo "\n";
                            print_r($processedCells);
                            echo "\n";
                            die("No se puede procesar [{$cellDelimiter}]: {$rawCells[$i]} ($entry)");
                        }
                    }
                    if (count($processedCells) == $columnCount) {
                        $dataset[] = $processedCells;
                    } else {
                        print_r($processedCells);
                        die("No se puede procesar: $entry (columnCount=$columnCount) != (processedCells=" . count($processedCells) . ")");
                    }
                }
            }

            //si la primera fila no es el título de los campos
            else {
                if (!empty($entry)) {

                    $matches = array();
                    if (preg_match_all('/(,)/', $entry, $matches)) {
                      if (count($matches[1]) > 1) {
                      $cellDelimiter = ',';
                      $columnCount = count($matches[1]) + 1;

                      }}elseif (preg_match_all('/(;)/', $entry, $matches)) {

                        if (count($matches[1]) > 1) {
                            $cellDelimiter = ';';
                            //numero de campos = numero de ; +1
                            $columnCount = count($matches[1]) + 1;
                        }
                        //cambiamos el delimitador por un tabulador
                    } elseif (preg_match_all('/(\t)/', $entry, $matches)) {
                        if (count($matches[1]) > 1) {
                            $cellDelimiter = "\t";
                            $columnCount = count($matches[1]) + 1;
                        }
                    } else {
                        // Ni idea...
                    }


                    $rawCells = explode($cellDelimiter, $entry);
                    $processedCells = array();

                    $currentCellContent = '';
                    //recorro cada entrada guardada en $rawCells


                    for ($i = 0; $i < count($rawCells); $i++) {
                        $match = array();
                        if (!$currentCellContent && preg_match('/^(\d+)$/', $rawCells[$i], $match)) {
                            $processedCells[] = $match[1];
                        } elseif (!$currentCellContent && preg_match('/^([^"].*[^"])$/', $rawCells[$i], $match)) {
                            $processedCells[] = str_replace('""', '"', $match[1]);
                        } elseif (!$currentCellContent && preg_match('/^"([^"].*[^"])"$/', $rawCells[$i], $match)) {
                            $processedCells[] = str_replace('""', '"', $match[1]);
                        } elseif (!$currentCellContent && preg_match('/^"([^"])"$/', $rawCells[$i], $match)) {
                            $processedCells[] = $match[1];
                        } elseif (!$currentCellContent && preg_match('/^([^"])$/', $rawCells[$i], $match)) {
                            $processedCells[] = $match[1];
                        } elseif (preg_match('/^([^"].+[^"]|[^"]+)"$/', $rawCells[$i], $match)) {
                            $processedCells[] = str_replace('""', '"', $currentCellContent . $cellDelimiter . $match[1]);
                            $currentCellContent = '';
                        } elseif (preg_match('/^"([^"].+[^"]|[^"]+)$/', $rawCells[$i], $match)) {
                            $currentCellContent = $match[1];
                        } elseif (preg_match('/^([^"].+[^"]|[^"]+)$/', $rawCells[$i], $match)) {
                            $currentCellContent .= $cellDelimiter . $match[1];
                        } elseif (preg_match('/^$/', $rawCells[$i])) {
                            $processedCells[] = '';
                        } elseif (preg_match('//', $cellDelimiter, $match)) {

                        } else {
                            print_r($rawCells);
                            echo "\n";
                            print_r($processedCells);
                            echo "\n";
                            die("No se puede procesar [{$cellDelimiter}]: {$rawCells[$i]} ($entry)");
                        }
                    }

                    if (count($processedCells) == $columnCount) {
                        $dataset[] = $processedCells;
                    } else {
                        print_r($processedCells);
                        die("No se puede procesar: $entry (columnCount=$columnCount) != (processedCells=" . count($processedCells) . ")");
                    }

                }
            }
        }

       $this->context->smarty->assign('datasetlength', count($dataset[0]));
       $this->context->smarty->assign('dataset', $dataset);

       $this->context->smarty->assign('HOOK_LEFT_COLUMN', null);
       $this->context->smarty->assign('HOOK_RIGHT_COLUMN', null);

    }

}
