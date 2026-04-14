<?php
require_once __DIR__ . '/i18n.php';
session_start();
require_once __DIR__ . '/../config.php';
panelSyncLangFromSetting();
require_once __DIR__ . '/../function.php';
$query = $pdo->prepare("SELECT * FROM admin WHERE username=:username");
    $query->bindParam("username", $_SESSION["user"], PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);
    $query = $pdo->prepare("SELECT * FROM product");
    $query->execute();
    $listinvoice = $query->fetchAll();
    $query = $pdo->prepare("SELECT * FROM marzban_panel");
    $query->execute();
    $listpanel = $query->fetchAll();
if( !isset($_SESSION["user"]) || !$result ){
    header('Location: login.php');
    return;
}
$__panelHtml = panelHtmlAttrs();
$nameProduct = $_POST['nameproduct'] ?? null;
if(!empty($nameProduct)){
    $randomString = bin2hex(random_bytes(2));
    $userdata['data_limit_reset'] = "no_reset";
    $product = select("product","*","name_product",$nameProduct,"count");
    if($product != 0){
        echo '<script>alert(' . json_encode(panelT('product_exists_alert'), JSON_UNESCAPED_UNICODE) . ');</script>';
        return;
    }
    $hidepanel = "{}";
    $stmt = $pdo->prepare("INSERT IGNORE INTO product (name_product,code_product,price_product,Volume_constraint,Service_time,Location,agent,data_limit_reset,note,category,hide_panel,one_buy_status) VALUES (:name_product,:code_product,:price_product,:Volume_constraint,:Service_time,:Location,:agent,:data_limit_reset,:note,:category,:hide_panel,'0')");
    $stmt->bindParam(':name_product', $nameProduct, PDO::PARAM_STR);
    $stmt->bindParam(':code_product', $randomString);
    $stmt->bindParam(':price_product', $_POST['price_product'] ?? '', PDO::PARAM_STR);
    $stmt->bindParam(':Volume_constraint', $_POST['volume_product'] ?? '', PDO::PARAM_STR);
    $stmt->bindParam(':Service_time', $_POST['time_product'] ?? '', PDO::PARAM_STR);
    $stmt->bindParam(':Location', $_POST['namepanel'] ?? '', PDO::PARAM_STR);
    $stmt->bindParam(':agent', $_POST['agent_product'] ?? '', PDO::PARAM_STR);
    $stmt->bindParam(':data_limit_reset', $userdata['data_limit_reset']);
    $stmt->bindParam(':category', $_POST['cetegory_product'] ?? ''  , PDO::PARAM_STR);
    $stmt->bindParam(':note', $_POST['note_product'] ?? ''  , PDO::PARAM_STR);
    $stmt->bindParam(':hide_panel', $hidepanel);
    $stmt->execute();
    header("Location: product.php");
}
if(isset($_GET['oneproduct'], $_GET['toweproduct']) && $_GET['oneproduct'] !== '' && $_GET['toweproduct'] !== ''){
    update("product", "id", 10000, "id", $_GET['oneproduct']);
    update("product", "id", intval($_GET['oneproduct']), "id", intval($_GET['toweproduct']));
    update("product", "id", intval($_GET['toweproduct']), "id", 10000);
    header("Location: product.php");
}

if(isset($_GET['removeid']) && $_GET['removeid'] !== ''){
    $stmt = $connect->prepare("DELETE FROM product WHERE id = ?");
    $stmt->bind_param("s", $_GET['removeid']);
    $stmt->execute();
    header("Location: product.php");
}
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($__panelHtml['lang']); ?>" dir="<?php echo htmlspecialchars($__panelHtml['dir']); ?>">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="Mosaddek">
    <meta name="keyword" content="FlatLab, Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">
    <link rel="shortcut icon" href="img/favicon.html">

    <title><?php echo htmlspecialchars(panelT('page_title')); ?></title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-reset.css" rel="stylesheet">
    <!--external css-->
    <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/jquery-easy-pie-chart/jquery.easy-pie-chart.css" rel="stylesheet" type="text/css" media="screen"/>
    <link rel="stylesheet" href="css/owl.carousel.css" type="text/css">
    <!-- Custom styles for this template -->
    <link href="css/style.css" rel="stylesheet">
    <link href="css/style-responsive.css" rel="stylesheet" />
    <link href="css/panel-i18n.css" rel="stylesheet" />

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 tooltipss and media queries -->
    <!--[if lt IE 9]>
      <script src="js/html5shiv.js"></script>
      <script src="js/respond.min.js"></script>
    <![endif]-->
  </head>


<body class="panel-lang-<?php echo htmlspecialchars(panelCurrentLanguage()); ?>">

    <section id="container" class="">
<?php include("header.php");
?>
        <!--main content start-->
        <section id="main-content">
            <section class="wrapper">
                <!-- page start-->
                <div class="row">
                    <div class="col-lg-12">
                        <section class="panel">
                            <header class="panel-heading"><?php echo htmlspecialchars(panelT('products_list')); ?></header>
                                <section class="panel">
                                <a href="#addproduct" data-toggle="modal"  class="btn btn-info  btn-sm"><?php echo htmlspecialchars(panelT('btn_add_product')); ?></a>
                                <a href="#moveradif" data-toggle="modal"  class="btn btn-success  btn-sm"><?php echo htmlspecialchars(panelT('btn_reorder_products')); ?></a>
                        </section>
                            <table class="table table-striped border-top" id="sample_1">
                                <thead>
                                    <tr>
                                        <th style="width: 8px;">
                                            <input type="checkbox" class="group-checkable" data-set="#sample_1 .checkboxes" /></th>
                                        <th class="hidden-phone"><?php echo htmlspecialchars(panelT('col_id')); ?></th>
                                        <th class="hidden-phone"><?php echo htmlspecialchars(panelT('col_service_code')); ?></th>
                                        <th class="hidden-phone"><?php echo htmlspecialchars(panelT('col_service_name')); ?></th>
                                        <th><?php echo htmlspecialchars(panelT('col_service_price')); ?></th>
                                        <th class="hidden-phone"><?php echo htmlspecialchars(panelT('col_service_volume')); ?></th>
                                        <th class="hidden-phone"><?php echo htmlspecialchars(panelT('col_service_time')); ?></th>
                                        <th class="hidden-phone"><?php echo htmlspecialchars(panelT('col_service_loc')); ?></th>
                                        <th class="hidden-phone"><?php echo htmlspecialchars(panelT('col_agent_group')); ?></th>
                                        <th class="hidden-phone"><?php echo htmlspecialchars(panelT('col_reset_cycle')); ?></th>
                                        <th class="hidden-phone"><?php echo htmlspecialchars(panelT('col_category')); ?></th>
                                        <th class="hidden-phone"><?php echo htmlspecialchars(panelT('col_operation')); ?></th>
                                    </tr>
                                </thead>
                                <tbody> <?php
                                foreach($listinvoice as $list){
                                    if($list['category'] == null){
                                        $list['category'] = panelT('none');
                                    }
                                   $bd = htmlspecialchars(panelT('btn_delete_product'), ENT_QUOTES, 'UTF-8');
                                   $be = htmlspecialchars(panelT('btn_edit_product'), ENT_QUOTES, 'UTF-8');
                                   echo "<tr class=\"odd gradeX\">
                                        <td>
                                        <input type=\"checkbox\" class=\"checkboxes\" value=\"1\" /></td>
                                        <td>{$list['id']}</td>
                                        <td>{$list['code_product']}</td>
                                        <td class=\"hidden-phone\">{$list['name_product']}</td>
                                        <td class=\"hidden-phone\">{$list['price_product']}</td>
                                        <td class=\"hidden-phone\">{$list['Volume_constraint']}</td>
                                        <td class=\"hidden-phone\">{$list['Service_time']}</td>
                                        <td class=\"hidden-phone\">{$list['Location']}</td>
                                        <td class=\"hidden-phone\">{$list['agent']}</td>
                                        <td class=\"hidden-phone\">{$list['data_limit_reset']}</td>
                                        <td class=\"hidden-phone\">{$list['category']}</td>
                                        <td  class=\"hidden-phone\"><a class = \"btn btn-danger\" href= \"product.php?removeid={$list['id']}\">$bd</a><a class = \"btn btn-info\" href= \"productedit.php?id={$list['id']}\">$be</a></td>
                                    </tr>";
                                }
                                    ?>
                                </tbody>
                            </table>
                        </section>
                    </div>
                </div>
                <!-- page end-->
            </section>
        </section>
        <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="moveradif" class="modal fade">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                                                <h4 class="modal-title"><?php echo htmlspecialchars(panelT('modal_swap_title')); ?></h4>
                                            </div>
                                            <div class="modal-body">
                                                <form action = "product.php" method = "GET" class="form-horizontal" role="form">
                                                    <div class="form-group">
                                                        <label for="oneproduct" class="col-lg-2 control-label"><?php echo htmlspecialchars(panelT('label_first_id')); ?></label>
                                                        <div class="col-lg-10">
                                                            <input type="number" name = "oneproduct" class="form-control" id="oneproduct">
                                                        </div>
                                                        <label for="toweproduct" class="col-lg-2 control-label" style = "margin:20px 0;"><?php echo htmlspecialchars(panelT('label_second_id')); ?></label>
                                                        <div class="col-lg-10" style = "margin:20px 0;">
                                                            <input type="number" name = "toweproduct" class="form-control" id="toweproduct">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="col-lg-offset-2 col-lg-10">
                                                            <button type="submit" class="btn btn-default"><?php echo htmlspecialchars(panelT('btn_swap_submit')); ?></button>
                                                        </div>
                                                    </div>
                                                </form>

                                            </div>

                                        </div>
                                    </div>
                                </div>
        <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="addproduct" class="modal fade">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                                                <h4 class="modal-title"><?php echo htmlspecialchars(panelT('modal_add_product')); ?></h4>
                                            </div>
                                            <div class="modal-body">
                                                <form action = "product.php" method = "POST" class="form-horizontal" role="form">
                                                    <div class="form-group">
                                                        <label for="nameproduct" class="col-lg-2 control-label"><?php echo htmlspecialchars(panelT('label_product_name')); ?></label>
                                                        <div class="col-lg-10"><input required type="text" name = "nameproduct" class="form-control" id="nameproduct"></div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="nameproduct" class="col-lg-2 control-label"><?php echo htmlspecialchars(panelT('label_product_location')); ?></label>
                                                        <div class="col-lg-10">
                                                        <select required  name = "namepanel" class="form-control">
                                                  <option value="/all"><?php echo htmlspecialchars(panelT('opt_all_panels')); ?></option>
                                                <?php
                                                if(count($listpanel)>=0){
                                                foreach($listpanel as $panel){
                                                echo "<option value = \"{$panel['name_panel']}\">{$panel['name_panel']}</option>";
                                                }
                                                }
                                                ?>
                                            </select>
                                                    </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="price_product" class="col-lg-2 control-label"><?php echo htmlspecialchars(panelT('label_price')); ?></label>
                                                        <div class="col-lg-10"><input  required type="number" name = "price_product" class="form-control" id="price_product"></div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="volume_product" class="col-lg-2 control-label"><?php echo htmlspecialchars(panelT('label_volume')); ?></label>
                                                        <div class="col-lg-10"><input required type="number" name = "volume_product" class="form-control" id="volume_product"></div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="time_product" class="col-lg-2 control-label"><?php echo htmlspecialchars(panelT('label_duration')); ?></label>
                                                        <div class="col-lg-10"><input required type="number" name = "time_product" class="form-control" id="volume_product"></div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="agent_product" class="col-lg-2 control-label"><?php echo htmlspecialchars(panelT('label_user_type_product')); ?></label>
                                                        <div class="col-lg-10">
                                                        <select required  name = "agent_product" class="form-control">
                                                 <option value="f"><?php echo htmlspecialchars(panelT('opt_user_normal')); ?></option>
                                                <option value = "n"><?php echo htmlspecialchars(panelT('opt_agent')); ?></option>
                                                <option value = "n2"><?php echo htmlspecialchars(panelT('opt_agent_plus')); ?></option>
                                            </select>
                                                    </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="note_product" class="col-lg-2 control-label"><?php echo htmlspecialchars(panelT('label_note')); ?></label>
                                                        <div class="col-lg-10"><input required type="text" name = "note_product" class="form-control" id="note_product"></div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="cetegory_product" class="col-lg-2 control-label"><?php echo htmlspecialchars(panelT('label_category')); ?></label>
                                                        <div class="col-lg-10"><input required type="text" name = "cetegory_product" class="form-control" id="cetegory_product"></div>
                                                    </div>

                                                    <div class="form-group">
                                                        <div class="col-lg-offset-2 col-lg-10">
                                                            <button type="submit" class="btn btn-danger"><?php echo htmlspecialchars(panelT('btn_add_submit')); ?></button>
                                                        </div>
                                                    </div>
                                                </form>

                                            </div>

                                        </div>
                                    </div>
                                </div>

        <!--main content end-->
    </section>

    <!-- js placed at the end of the document so the pages load faster -->
    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.scrollTo.min.js"></script>
    <script src="js/jquery.nicescroll.js" type="text/javascript"></script>
    <script type="text/javascript" src="assets/data-tables/jquery.dataTables.js"></script>
    <script type="text/javascript" src="assets/data-tables/DT_bootstrap.js"></script>


    <!--common script for all pages-->
    <script src="js/common-scripts.js"></script>

    <!--script for this page only-->
    <script>window.__PANEL_DT_LANG = <?php echo panelDataTablesLanguageJson(); ?>;</script>
    <script src="js/dynamic-table.js"></script>


</body>
</html>
    