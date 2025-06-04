<script src="/{$template_catalog}/template/{$themes}/js/common/jquery.mini.js"></script>
<link rel="stylesheet" href="/{$template_catalog_cart}/template/{$themes_cart}/css/goods_iframe.css">
</head>

<body>
  <!-- mounted之前显示 -->
  <div id="mainLoading">
    <div class="ddr ddr1"></div>
    <div class="ddr ddr2"></div>
    <div class="ddr ddr3"></div>
    <div class="ddr ddr4"></div>
    <div class="ddr ddr5"></div>
  </div>
  <div class="goods">
    <el-container>
      <aside-menu v-show="false"></aside-menu>
      <el-container>
        <top-menu :num="shoppingCarNum" v-show="false"></top-menu>
        <!-- 自己的东西 -->
        <!-- 后端渲染出来的配置页面 -->
        <div class="config-box">
          <div class="content"></div>
        </div>
      </el-container>
    </el-container>
  </div>
  <!-- =======页面独有======= -->
  <script src="/{$template_catalog}/template/{$themes}/components/payDialog/payDialog.js"></script>
  <script src="/{$template_catalog_cart}/template/{$themes_cart}/api/product.js"></script>
  <script src="/{$template_catalog_cart}/template/{$themes_cart}/js/goods.js"></script>
  <script src="/{$template_catalog}/template/{$themes}/components/pagination/pagination.js"></script>
  <script src="/{$template_catalog}/template/{$themes}/components/discountCode/discountCode.js"></script>
  <script src="/{$template_catalog}/template/{$themes}/components/eventCode/eventCode.js"></script>
  <script src="/{$template_catalog}/template/{$themes}/components/customGoods/customGoods.js"></script>