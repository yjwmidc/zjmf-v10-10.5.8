{include file="header"}
<link rel="stylesheet" href="/{$template_catalog}/template/{$themes}/css/home.css">
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
  <div class="template">
    <el-container>
      <aside-menu></aside-menu>
      <el-container>
        <top-menu></top-menu>
        <el-main>
          <!-- 自己的东西 -->
          <div class="main-card">
            <div class="main-content">
              <div class="left-box">
                <div class="info-box">
                  <div class="info-first" @click="goUser" v-loading="nameLoading">
                    <div class="name-first" ref="headBoxRef">
                      {{account.firstName}}
                    </div>
                    <div class="name-box">
                      <p class="hello" :title="account.username">
                        {{lang.index_hello}},{{account.username}}
                        <span v-if="idcsmart_client_level.id"
                          :style="{'color':idcsmart_client_level.background_color}">({{idcsmart_client_level.name}})
                        </span>
                      </p>
                      <p class="name">
                        ID：<span class="id-text">{{account.id}}</span>
                      </p>
                    </div>
                  </div>
                  <el-divider class="divider-box" direction="vertical"></el-divider>
                  <div class="info-second" v-loading="nameLoading">
                    <div class="email-box">
                      <span><img src="/{$template_catalog}/template/{$themes}/img/home/email-icon.png"
                          alt="">{{lang.index_email}}</span>
                      <span class="phone-number">{{account.email ? account.email : '--'}}</span>
                    </div>
                    <div class="phone-box">
                      <span><img src="/{$template_catalog}/template/{$themes}/img/home/tel-icon.png"
                          alt="">{{lang.index_tel}}</span>
                      <span class="phone-number">{{account.phone ? account.phone : '--'}}</span>
                    </div>
                  </div>
                  <el-divider class="divider-box" direction="vertical"></el-divider>
                  <div class="info-three" v-plugin="'IdcsmartCertification'"
                    v-if="certificationObj.certification_open === 1">
                    <div class="compny-box">
                      <div class="left-icon">
                        <img src="/{$template_catalog}/template/{$themes}/img/home/compny-icon.png" alt="">
                        <span class="left-type">{{lang.index_compny}}</span>
                      </div>
                      <div class="right-text">
                        <div class="right-title">
                          <span class="company-name"
                            v-if="certificationObj.company?.status === 1">{{certificationObj.company.certification_company}}</span>
                          <span class="company-name bule-text" @click="handelAttestation"
                            v-else>{{lang.index_goAttestation}}</span>
                        </div>
                        <div class="certify-id">
                          <div class="right-type">{{lang.finance_custom23}}：</div>
                          <div class="company-name certify-bottom" :title="certificationObj.company?.certify_id">
                            <span
                              class="certify-text">{{certificationObj.company?.certify_id ? certificationObj.company.certify_id : '--'}}</span>
                            <img class="cpoy-btn" v-copy="certificationObj.company.certify_id"
                              v-if="certificationObj.company?.certify_id"
                              src="/{$template_catalog}/template/{$themes}/img/home/copy.svg" alt="">
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="person-box">
                      <div class="left-icon">
                        <img class="left-icon" src="/{$template_catalog}/template/{$themes}/img/home/person-icon.png"
                          alt="">
                        <span class="left-type">{{lang.index_name}}</span>
                      </div>
                      <div class="right-text">
                        <div class="right-title">
                          <span class="company-name" v-if="certificationObj.is_certification"
                            :title="certificationObj.company.status === 1 ? certificationObj.company.card_name : certificationObj.person.card_name">
                            {{certificationObj.company.status === 1 ? certificationObj.company.card_name : certificationObj.person.card_name}}
                          </span>
                          <span class="company-name bule-text" @click="handelAttestation"
                            v-else>{{lang.index_goAttestation}}</span>
                        </div>
                        <div class="certify-id">
                          <div class="right-type">{{lang.finance_custom24}}：</div>
                          <div class="company-name certify-bottom" :title="certificationObj.person?.certify_id">
                            <span
                              class="certify-text">{{certificationObj.person?.certify_id ? certificationObj.person.certify_id : '--'}}
                            </span>
                            <img v-copy="certificationObj.person?.certify_id" v-if="certificationObj.person?.certify_id"
                              class="cpoy-btn" src="/{$template_catalog}/template/{$themes}/img/home/copy.svg" alt="">
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="statistics-box">
                  <h3 class="title-text">{{lang.index_text1}}</h3>
                  <div class="statistics-content" v-loading="nameLoading">
                    <div class="money-box">
                      <div class="statistics-top">
                        <div class="statistics-credit">
                          <div class="credit-title">
                            <div>{{lang.index_text3}}</div>
                          </div>
                          <div class="credit-money">
                            <div class="credit-num">{{commonData.currency_prefix}}{{account.credit}}</div>
                            <div class="recharge-btn" @click="showCz" v-if="commonData.recharge_open == 1">
                              {{lang.index_text2}}
                            </div>
                          </div>
                        </div>
                        <div class="statistics-credit" v-if="isShowCredit && creditData.status">
                          <div class="credit-title">
                            <div>{{lang.finance_text38}}</div>
                            <div class="credit-tag" v-if="creditData.status === 'Expired'">{{lang.finance_text93}}</div>
                            <div class="credit-tag" v-if="creditData.status === 'Overdue'">{{lang.finance_text94}}</div>
                            <div class="credit-tag" v-if="creditData.status === 'Active'">{{lang.finance_text95}}</div>
                            <div class="credit-tag" v-if="creditData.status === 'Suspended'">{{lang.finance_text96}}
                            </div>
                          </div>
                          <div class="credit-money">
                            <div class="credit-num">
                              {{commonData.currency_prefix}}{{creditData.remaining_amount}}
                            </div>
                            <div class="recharge-text" @click="goCredit">
                              {{lang.index_text34}}
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="voucher-box" v-if="voucherList.length > 0">
                        {{lang.index_text24}}
                        <a href="/finance.htm?tab=4" target="_blank" class="bule-text">
                          {{lang.index_text25}}
                        </a>
                      </div>
                      <div class="statistics-bottom">
                        <div class="progress-box">
                          <el-progress type="circle" :width="Number(117)" :stroke-width="Number(12)" color='#04C8C9'
                            :show-text="false" :percentage="percentage"></el-progress>
                        </div>
                        <div class="statistics-bottom-right">
                          <div class="money-month">
                            <div>
                              <span class="type-box green-bg"></span>
                              <span>{{lang.index_text4}}
                                <span v-if="Number(account.this_month_consume_percent) >= 0"
                                  class="percent-box-green">↑{{Number(account.this_month_consume_percent)}}%</span>
                                <span v-else class="percent-box-red">↓{{Number(account.this_month_consume_percent)
                                  *-1}}%</span>
                              </span>
                            </div>
                            <div class="money-num">{{commonData.currency_prefix}}{{account.this_month_consume}}</div>
                          </div>
                          <div class="money-total">
                            <div><span class="type-box grey-bg"></span>
                              <span>{{lang.index_text5}}</span>
                            </div>
                            <div class="money-num">{{commonData.currency_prefix}}{{account.consume}}</div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="order-box">
                      <div class="order-item order-box-1">
                        <div class="order-type-img">
                          <img src="/{$template_catalog}/template/{$themes}/img/home/activation-icon.png" alt="">
                        </div>
                        <h3 class="order-title">{{lang.index_text6}}</h3>
                        <div class="order-nums">{{account.host_active_num}}</div>
                      </div>
                      <div class="order-item order-box-2">
                        <div class="order-type-img">
                          <img src="/{$template_catalog}/template/{$themes}/img/home/prduct-icon.png" alt="">
                        </div>
                        <h3 class="order-title">{{lang.index_text7}}</h3>
                        <div class="order-nums">{{account.host_num}}</div>
                      </div>
                      <div class="order-item order-box-3">
                        <div class="order-type-img">
                          <img src="/{$template_catalog}/template/{$themes}/img/home/no-pay-order.png" alt="">
                        </div>
                        <h3 class="order-title">{{lang.index_text8}}</h3>
                        <div class="order-nums">{{account.unpaid_order}}</div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="product-list-box">
                  <h3 class="title-text">{{lang.index_text9}}</h3>
                  <div class="goods-box" v-loading="productListLoading">
                    <table class="goods-table">
                      <thead>
                        <tr>
                          <td>{{lang.index_text10}}</td>
                          <td>{{lang.index_text12}}</td>
                          <td class="time-box">{{lang.index_text13}}</td>
                          <td>{{lang.invoice_text139}}</td>
                        </tr>
                      </thead>
                      <tbody v-if="productList.length !== 0">
                        <tr v-for="item in productList" :key="item.id" class="product-item"
                          @click="goProductPage(item.id)">
                          <td>{{item.product_name}}</td>
                          <!-- <td>{{item.type ? item.type : '--'}}</td> -->
                          <td>{{item.name}}</td>
                          <td :class="item.isOverdue ? 'red-time' : ''">{{item.due_time | formateTime}}</td>
                          <td>{{item.client_notes}}</td>
                        </tr>
                      </tbody>
                    </table>
                    <div v-if="productList.length === 0 && !productListLoading" class="no-product">
                      <h2>{{lang.index_text14}}</h2>
                      <p>{{lang.index_text15}}</p>
                      <el-button @click="goGoodsList">{{lang.index_text16}}</el-button>
                    </div>
                  </div>
                </div>
              </div>
              <div class="right-box">
                <!-- 推介计划开始 -->

                <div class="recommend-box-open" v-if="showRight && isOpen" v-plugin="'IdcsmartRecommend'">
                  <div class="recommend-top">
                    <div class="left">
                      <div class="row1">
                        <div class="title-text">{{lang.referral_title1}}</div>
                        <span class="reword" @click="toReferral"><img
                            src="/{$template_catalog}/template/{$themes}/img/home/reword.png"
                            alt="">{{lang.referral_text14}}</span>
                      </div>
                      <div class="row2">{{lang.referral_title6}}</div>
                      <div class="row3">{{lang.referral_text15}}</div>
                      <div class="row4">{{lang.referral_text16}}</div>
                    </div>
                    <img class="right" src="/{$template_catalog}/template/{$themes}/img/home/credit-card.png" alt="">
                  </div>
                  <div class="url">
                    <div class="url-text" :title="promoterData.url">{{promoterData.url}}</div>
                    <div class="copy-btn" @click="copyUrl(promoterData.url)">{{lang.referral_btn2}}</div>
                  </div>
                  <div class="top-statistic">
                    <div class="top-item">
                      <div class="item-top">
                        <div class="top-money">{{commonData.currency_prefix}}{{promoterData.withdrawable_amount}}</div>
                        <div class="top-text">{{lang.referral_title2}}</div>
                      </div>
                      <img class="top-img" src="/{$template_catalog}/template/{$themes}/img/referral/top1.png" />
                    </div>
                    <div class="top-item">
                      <div class="item-top">
                        <div class="top-money">{{commonData.currency_prefix}}{{promoterData.pending_amount}}
                          <!-- <div class="icon-help" :title="`${lang.referral_text7}：${commonData.currency_prefix}${promoterData.frozen_amount}`">?</div> -->
                        </div>
                        <div class="top-text">{{lang.referral_title4}}</div>
                      </div>
                      <img class="top-img" src="/{$template_catalog}/template/{$themes}/img/referral/top3.png" />
                    </div>
                  </div>
                </div>
                <div class="recommend-box" v-if="!showRight || !isOpen">
                  <img src="/{$template_catalog}/template/{$themes}/img/home/recommend-img.png" alt="">
                  <div v-if="showRight">
                    <h2>{{lang.index_text17}}</h2>
                    <p>{{lang.index_text18}}</p>
                    <div class="no-recommend" @click="openVisible = true">{{lang.index_text28}}</div>
                  </div>
                  <div v-else class="recommend-text">{{lang.index_text21}}</div>
                </div>
                <!-- 推介计划结束 -->

                <div class="WorkOrder-box" v-if="ticketList.length !==0 " v-plugin="'IdcsmartTicket'">
                  <div class="title-text WorkOrder-title">
                    <div>{{lang.index_text22}}</div>
                    <div class="more" @click="goWorkPage">···</div>
                  </div>
                  <div class="WorkOrder-content">
                    <div class="WorkOrder-item" v-for="item in ticketList" :key="item.id"
                      @click="goTickDetail(item.id)">
                      <div class="replay-div" :style="{'background':`${item.color}`}">{{item.status}}</div>
                      <div class="replay-box">
                        <div class="replay-title">#{{item.ticket_num}} - {{item.title}}</div>
                        <div class="replay-name">{{item.name}}</div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="notice-box" v-if="homeNewList.length !==0" v-plugin="'IdcsmartNews'">
                  <div class="title-text WorkOrder-title">
                    <div>{{lang.index_text23}}</div>
                    <div class="more" @click="goNoticePage">···</div>
                  </div>
                  <div class="WorkOrder-content">
                    <div v-for="item in homeNewList" :key="item.id" class="notice-item"
                      @click="goNoticeDetail(item.id)">
                      <div class="notice-item-left">
                        <h3 class="notice-time">{{item.create_time | formareDay}}</h3>
                        <h4 class="notice-title">{{item.title}}</h4>
                        <h5 class="notice-type">{{item.type}}</h5>
                      </div>
                      <div class="notice-item-right"><i class="el-icon-arrow-right"></i></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- 充值 dialog -->
          <div class="cz-dialog">
            <el-dialog width="6.8rem" :visible.sync="isShowCz" @close="czClose">
              <div class="dialog-title">
                {{lang.finance_title4}}
              </div>
              <div class="dialog-form">
                <el-form :model="czData" label-position="top" @submit.native.prevent>
                  <!-- <el-form-item :label="lang.finance_label18">
                        <el-select v-model="czData.gateway" @change="czSelectChange">
                          <el-option v-for="item in gatewayList" :key="item.id" :label="item.title" :value="item.name"></el-option>
                        </el-select>
                      </el-form-item> -->
                  <el-form-item :label="lang.finance_label19" prop="amount">
                    <div class="cz-input">
                      <!-- <el-input v-model="czData.amount" @keyup.native="czData.amount=oninput(czData.amount)"> -->
                      <el-input v-model="czData.amount" @keypress.enter.native="czInputChange"
                        @change="czData.amount=oninput(czData.amount)">
                      </el-input>
                      <el-button class="btn-ok" @click="czInputChange">{{lang.finance_btn6}}</el-button>
                    </div>
                    <div v-html="commonData.recharge_money_notice_content"></div>
                  </el-form-item>
                  <!-- <el-form-item v-if="errText">
                        <el-alert :title="errText" type="error" :closable="false" show-icon>
                        </el-alert>
                      </el-form-item>
                      <el-form-item v-loading="payLoading1">
                        <div class="pay-html" v-show="isShowimg1" v-html="payHtml"></div>
                      </el-form-item> -->
                </el-form>
              </div>
            </el-dialog>
          </div>
          <!-- 确认开启弹窗 -->
          <el-dialog :title="lang.referral_title8" :visible.sync="openVisible" width="4.8rem"
            custom-class="open-dialog">
            <span>{{lang.referral_tips7}}</span>
            <span slot="footer" class="dialog-footer">
              <el-button class="btn-ok" type="primary" @click="openReferral">{{lang.referral_btn6}}</el-button>
              <el-button class="btn-no" @click="openVisible = false">{{lang.referral_btn7}}</el-button>
            </span>
          </el-dialog>
          <pay-dialog ref="payDialog" @payok="paySuccess" @paycancel="payCancel"></pay-dialog>
          <!-- 微信公众号 -->
          <div class="wx-code" v-if="hasWxPlugin && conectInfo.is_subscribe === 0">
            <el-popover width="200" trigger="hover" @show="getWxcode" placement="left">
              <div class="wx-box">
                <p class="tit">{{lang.wx_tip1}}</p>
                <div class="img" v-loading="codeLoading">
                  <img :src="wxQrcode" alt="" v-if="wxQrcode">
                </div>
              </div>
              <div class="wx-img" slot="reference"></div>
            </el-popover>
          </div>
        </el-main>
      </el-container>
    </el-container>
  </div>
  <!-- =======页面独有======= -->
  <script src="/{$template_catalog}/template/{$themes}/api/finance.js"></script>
  <script src="/{$template_catalog}/template/{$themes}/api/home.js"></script>
  <script src="/{$template_catalog}/template/{$themes}/js/home.js"></script>
  <script src="/{$template_catalog}/template/{$themes}/components/payDialog/payDialog.js"></script>

  {include file="footer"}
