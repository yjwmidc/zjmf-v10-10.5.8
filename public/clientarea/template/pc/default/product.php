{include file="header"}
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
  <div class="product">
    <el-container>
      <aside-menu></aside-menu>
      <el-container>
        <top-menu></top-menu>
        <el-main>
          <!-- 自己的东西 -->
          <!-- 后端渲染出来的配置页面 -->
          <div class="config-box">
            <div class="content"></div>
          </div>
        </el-main>
      </el-container>
    </el-container>
  </div>
  <!-- =======页面独有======= -->
  <script src="/{$template_catalog}/template/{$themes}/js/common/jquery.mini.js"></script>
  <script src="/{$template_catalog}/template/{$themes}/api/product.js"></script>
  <script src="/{$template_catalog}/template/{$themes}/components/payDialog/payDialog.js"></script>
  <script src="/{$template_catalog}/template/{$themes}/components/safeConfirm/safeConfirm.js"></script>
  <script src="/{$template_catalog}/template/{$themes}/components/productFilter/productFilter.js"></script>
  <script src="/{$template_catalog}/template/{$themes}/components/pagination/pagination.js"></script>
  <script src="/{$template_catalog}/template/{$themes}/components/discountCode/discountCode.js"></script>
  <script src="/{$template_catalog}/template/{$themes}/components/cashCoupon/cashCoupon.js"></script>
  <script src="/{$template_catalog}/template/{$themes}/components/countDownButton/countDownButton.js"></script>
  <script src="/{$template_catalog}/template/{$themes}/components/batchRenewpage/batchRenewpage.js"></script>
  <script src="/{$template_catalog}/template/{$themes}/components/autoRenew/autoRenew.js"></script>
  <script src="/{$template_catalog}/template/{$themes}/js/product.js"></script>
  {include file="footer"}
