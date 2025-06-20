const lang_obj = {
  chinese: "中文簡體", // 用於在語言切換下拉中顯示
  english: "English",
  countryImg: "CN", // 用於顯示圖片，使用國家代碼大寫
  account_title1: "帳戶",
  account_menu1: "概要",
  account_tips20: "未認證，前往",
  account_tips21: "實名認證",
  account_tips24: "實名認證已完成",
  account_tips22: "個人認證已完成，前往",
  account_tips23: "企業認證",
  account_menu3: "基礎資料",
  account_label1: "姓名",
  account_label2: "語言",
  account_label3: "公司",
  account_label4: "國家",
  account_label5: "地址",
  account_menu4: "帳戶資訊",
  account_label6: "手機",
  account_label7: "郵箱",
  account_label8: "密碼",
  oauth_text5: "三方登入",
  oauth_text10: "確定取消關聯?",
  oauth_text9: "取消關聯",
  oauth_text8: "關聯",
  account_btn1: "保存",
  account_menu2: "操作日誌",
  cloud_tip_2: "請輸入你需要搜尋的內容",
  account_label9: "描述",

  monthly: "月付",
  quarterly: "季付",
  semiannually: "半年",
  annually: "年付",
  biennially: "兩年",
  triennially: "三年",
  onetime: "一次性",
  free: "免費",
  recurring_prepayment: "週期先付",
  recurring_postpaid: "週期後付",
  semiannually_pay: "半年付",
  account_label10: "建立時間",
  account_tips15: "已經到底~",
  subaccount_text56: "站內信",
  subaccount_text61: "刪除",
  subaccount_text62: "標記為已讀",
  subaccount_text63: "全部標記為已讀",
  placeholder_pre2: "請選擇",
  subaccount_text64: "查詢",
  subaccount_text58: "訊息內容",
  subaccount_text66: "已讀",
  subaccount_text67: "未讀",
  subaccount_text59: "接收時間",
  subaccount_text60: "訊息子類型",
  account_title2: "更改密碼",
  account_label11: "原始密碼",
  account_tips1: "請輸入原始密碼",
  account_tips4: "忘記密碼",
  account_tips5: "驗證碼修改",
  account_label12: "新密碼",
  account_tips2: "請輸入新密碼",
  account_label13: "確認密碼",
  account_tips3: "請確認密碼",
  account_btn2: "提交",
  account_btn3: "取消",
  account_label14: "電子郵件",
  account_label15: "手機號碼",
  account_tips6: "請輸入電子郵件",
  account_tips7: "請輸入手機號碼",
  account_tips8: "郵箱驗證碼",
  account_tips9: "手機驗證碼",
  tip1: "密碼 (6~32位元)",
  tip2: "再次確認密碼",
  account_title3: "驗證手機號碼",
  account_tips10: "使用手機",
  account_tips11: "驗證",
  account_label16: "驗證碼",
  account_btn4: "驗證",
  account_title4: "更改手機號碼",
  account_title5: "綁定手機號碼",
  account_tips16: "請輸入新手機號碼",
  account_title6: "驗證信箱",
  account_tips17: "使用郵件",
  account_tips18: "驗證",
  account_title7: "更改信箱",
  account_title8: "綁定信箱",
  account_tips19: "請輸入新信箱",
  agreement_text1: "更新時間",
  agreement_text2: "關鍵字",
  agreement_text3: "附件",
  finance_title: "財務",
  finance_text1: "當前餘額",
  finance_btn1: "儲值",
  finance_btn2: "提領",
  finance_btn9: "提現記錄",
  finance_text2: "待退款金額",
  finance_tab1: "訂單記錄",
  finance_btn10: "合併支付",
  finance_label1: "商品名稱",
  finance_label2: "金額",
  finance_label3: "時間",
  finance_label4: "狀態",
  finance_text3: "未付款",
  finance_text4: "已付款",
  finance_text17: "已退款",
  finance_label5: "付款方式",
  finance_text5: "餘額",
  finance_label6: "操作",
  finance_btn4: "刪除訂單",
  finance_btn3: "去付款",
  finance_text6: "已經到底~",
  finance_tab2: "交易記錄",
  finance_label7: "訂單ID",
  finance_label22: "訂單類型",
  finance_label8: "金額",
  finance_label9: "交易流水號",
  finance_tab3: "餘額記錄",
  finance_text18: "至",
  finance_text19: "開始日期",
  finance_text20: "結束日期",
  finance_text21: "請選擇類型",
  finance_label10: "備註",
  finance_label11: "類型",
  finance_text22: "我的代金券",
  voucher_get: "我要領券",
  voucher_min: "最低使用金額",
  voucher_rule: "使用規則",
  voucher_order_product: "使用時訂單中需包含下列產品",
  voucher_accout_product: "帳戶中需擁有並正在使用下列產品",
  voucher_no_product: "帳戶中未擁有任何產品",
  voucher_active: "帳戶中需存在啟動中的產品",
  voucher_onetime: "單一使用者此代金券只能使用一次",
  voucher_upgrade: "此代金券可在升降級訂單中使用",
  voucher_renew: "此代金券可在續費訂單中使用",
  voucher_upgrade_no: "此代金券不可在升降級訂單中使用",
  voucher_renew_no: "此代金券不可在續約訂單中使用",
  voucher_empty: "暫無代金券可領",
  voucher_has_get: "已領取",
  voucher_get_now: "立即領取",
  finance_text23: "合約管理",
  finance_text24: "甲方資訊管理",
  finance_text25: "申請合約",
  finance_text26: "請輸入合約ID、產品識別、編號",
  finance_text27: "搜尋",
  finance_text28: "合約ID",
  finance_text29: "產品內容",
  finance_text30: "基礎合約",
  finance_text31: "狀態",
  finance_text32: "快遞訊息",
  finance_text33: "簽約",
  finance_text34: "查看",
  finance_text35: "取消",
  invoice_text41: "預覽",
  finance_text36: "下載",
  finance_text37: "郵遞",
  finance_text38: "信用額度",
  finance_text39: "授信總額",
  finance_text40: "到期",
  finance_text41: "剩餘額度",
  finance_text42: "本期帳單待還",
  finance_text43: "全部待還",
  finance_text44: "還款截止",
  finance_text45: "未出帳",
  finance_text46: "立即還款",
  finance_text47: "出帳週期",
  finance_text48: "消費額度",
  finance_text49: "消費記錄",
  finance_text50: "還款",
  finance_title2: "訂單詳情",
  finance_label12: "標識",
  finance_title3: "申請提現",
  finance_label13: "提現方式",
  finance_label14: "帳號",
  finance_label15: "銀行卡號",
  finance_label16: "姓名",
  finance_label17: "提現金金額",
  finance_btn5: "全部",
  finance_btn6: "提交",
  finance_btn7: "取消",
  finance_title4: "儲值",
  finance_label19: "儲值金額",
  finance_text7: "確認刪除該訂單？",
  finance_btn8: "確認",
  finance_text51: "快遞訊息",
  finance_text52: "您的合約已發出，請注意查收",
  finance_text53: "快遞",
  finance_text54: "單號",
  finance_text55: "地址",
  finance_text56: "電話",
  finance_text57: "姓名",
  finance_text58: "關閉",
  finance_text59: "甲方資訊管理",
  finance_text60: "合約簽訂後具有法律效力！",
  finance_text61: "請仔細檢查您的甲方訊息，確認訊息的真實和完整。",
  finance_text62: "甲方名稱",
  finance_text63: "證件號碼",
  finance_text64: "甲方名稱",
  finance_text65: "請輸入",
  finance_text66: "證件號碼",
  finance_text67: "聯絡電話",
  finance_text68: "聯絡信箱",
  finance_text69: "聯絡地址",
  finance_text70: "儲存",
  finance_text71: "取消",
  finance_text72: "取消申請",
  finance_text73: "撤回申請後，若要再申請，需重新蓋章",
  finance_text74: "確認",
  finance_text75: "取消",
  finance_text76: "申請紙本合約",
  finance_text77:
    "電子合約法律效力等同於紙質合同，您可以直接在線下載打印合同適用，無需申請紙質合同。如業務一定需要紙質合同，請點擊“確定”按鈕申請紙質蓋章合同，我們將在 10個工作天內為您郵寄。",
  finance_text78: "收件人姓名",
  finance_text79: "收件者地址",
  finance_text80: "收件人電話",
  finance_text81: "快遞費用",
  finance_text82: "確認申請",
  finance_text83: "取消",
  finance_text84: "消費記錄",
  finance_text85: "付款時間",
  finance_text86: "關閉",

  support_trial: "支持試用",

  login_welcome: "歡迎來到",
  login_vip: "會員中心",
  login_level: "實現線上業務的便利交易與管理",
  forget: "忘記密碼",
  regist_yes_account: "已有帳戶？",
  regist_login_text: "立即登入",
  login_email: "電子郵件",
  login_phone: "手機號碼",
  email_code: "郵件信箱驗證碼",
  login_phone_code: "手機驗證碼",
  login_read: "閱讀並同意",
  read_service: "《服務協定》",
  read_and: "和",
  read_privacy: "《隱私權協議》",
  regist_to_login: "確認並登入",
  new_goods: "訂購產品",
  first_level: "一級分類",
  second_level: "二級分類",
  search_placeholder: "請輸入關鍵字搜尋",
  goods_search_placeholder: "關鍵詞搜索所有商品",
  search: "查詢",
  no_goods: "暫無商品",
  buy: "購買",
  goods_text1: "立折",
  goods_text2: "滿",
  goods_text3: "減",
  template_text93: "網域註冊",
  template_text94: "大量註冊",
  template_text92: "請輸入網域關鍵字，如wanwang",
  template_text95: "查詢網域",
  template_text96: "搜尋結果",
  template_text97: "已被註冊",
  template_text98: "溢價網域",
  common_cloud_text112: "年",
  template_text99: "新購",
  template_text100: "續費",
  template_text101: "年",
  template_text102: "新增",
  template_text103: "whois訊息",
  template_text104: "查詢失敗",
  template_text105: "開始搜尋網域名稱",
  template_text106: "1、請輸入精準域名，一行一個，回車即可新增多個",
  template_text107: "2、一次可支援批次查詢",
  template_text108: "個域名，批次導入時，超過",
  template_text109: "部分會自動刪除",
  template_text110: "3、匯入檔案格式為txt檔案",
  template_text111: "4、系統會自動過濾重複值",
  template_text112: "立即搜尋",
  template_text113: "可註冊域名",
  template_text114: "已被註冊",
  template_text115: "溢價域名",
  template_text116: "全選",
  template_text117: "全部新增",
  template_text118: "不可註冊域名",
  template_text119: "收起",
  template_text120: "展開",
  template_text121: "查詢失敗",
  template_text122: "大量搜尋網域",
  template_text123: "域名購物車",
  template_text124: "清空",
  template_text125: "請先",
  template_text126: "選購網域名稱",
  template_text127: "登陸帳號",
  template_text128: "移除",
  template_text129: "全選",
  template_text87: "合計",
  template_text130: "立即購買",
  goods_loading: "正在載入中....",
  no_more_goods: "沒有更多商品啦....",
  template_text131: "批次導入域名",
  template_text132: "導入檔案",
  template_text133: "請選擇檔案",
  template_text134: "選擇檔案",
  template_text135: "確認",
  template_text136: "取消",
  security_title: "安全",
  in_rules: "入方向規則",
  com_config: {
    select: "下拉選擇",
    multi_select: "下拉多選",
    radio: "點擊單選",
    quantity: "數量輸入",
    quantity_range: "數量拖曳",
    yes_no: "是否",
    area: "區域",
    cycle: "付款週期",
    please_select: "請選擇",
    yes: "是",
    no: "否",
    city: "城市",
    product_name: "商品名稱",
    query: "查詢",
    mark: "標識",
    money_cycle: "金額/週期",
    active_time: "訂購時間",
    add: "新增",
    select_pro_status: "請選擇產品狀態",
  },
  rules: "規則",
  batch_add: "批次新增",
  batch_delete: "批次刪除",
  protocol: "協議",
  port_range: "連接埠範圍",
  auth_ip: "授權IP",
  security_label3: "操作",
  edit: "編輯",
  security_btn4: "刪除",
  out_rules: "出方向規則",
  relation_instance: "關聯實例",
  unbind_safe: "解綁",
  cloud_menu_1: "實例",
  common_cloud_label13: "連接埠",
  security_tip2: "例如：22或22-12345",
  referral_btn6: "確定",
  referral_btn7: "取消",
  common_port: "常見協定連接埠",
  security_tip3: "實例將從已有安全性群組移除並新增至本安全性群組",
  security_btn9: "確認刪除",
  security_btn6: "取消",
  index_hello: "你好",
  index_email: "郵箱",
  index_tel: "電話",
  index_compny: "認證企業",
  index_goAttestation: "去認證",
  index_name: "認證姓名",
  index_text1: "統計",
  index_text2: "儲值",
  index_text3: "(當前餘額)",
  index_text4: "本月消費金額",
  index_text5: "總消費金額",
  index_text6: "激活產品數量",
  index_text7: "產品總量",
  index_text8: "未繳單",
  index_text9: "產品清單",
  index_text10: "產品名稱",
  index_text12: "主機編號",
  index_text13: "到期時間",
  invoice_text139: "備註",
  index_text14: "這裡什麼都沒有",
  index_text15: "在我們的全球資料中心位置部署新伺服器",
  index_text16: "部署伺服器",
  referral_title1: "推介計畫",
  referral_text14: "查看獎勵",
  referral_title6: "推介連結",
  referral_text15: "發送推介連結給有需要的人，邀請他人購買商品時，",
  referral_text16: "請將以下連結發給被推介者",
  referral_btn2: "複製連結",
  referral_title2: "可提現獎勵金額",
  referral_text7: "凍結金額",
  referral_title4: "待確認金額",
  index_text17: "商品推廣計劃",
  index_text18: "開通推介計劃，享受推介獎勵",
  index_text21: "管理者尚未開啟推介計畫",
  index_text28: "立刻開啟",
  index_text22: "最近工單",
  index_text23: "公告通知",
  index_text24: "存在可領取的代金券",
  index_text25: "前往領取",
  finance_label18: "儲值方式",
  referral_title8: "開啟",
  referral_tips7: "您將開啟推廣計劃，請確認是否繼續",
  login: "登入",
  login_no_account: "沒有帳戶？",
  login_regist_text: "立即註冊",
  login_placeholder_pre: "請輸入",
  login_pass: "密碼",
  login_forget: "忘記密碼？",
  login_code_login: "驗證碼登入",
  login_pass_login: "密碼登入",
  status_text3: "網路故障",
  status_text4: "重試",
  updatw_time: "更新時間",
  news_key: "關鍵字",
  news_annex: "附件",
  not_found_text1: "你沒有權限存取此頁面",
  not_found_text2: "你可以通知管理員授予權限",
  not_found_text3: "返回",
  status_text1: "頁面找不到了",
  status_text2: "返回",
  oauth_text1: "關聯帳號",
  oauth_text2: "請先關聯需要關聯的帳號",
  oauth_text3: "如果帳戶不存在將根據資訊自動建立新帳戶",
  oauth_text4: "提交",
  order_text1: "訂單詳情",
  order_text2: "訂單號碼：",
  order_text3: "訂單日期：",
  order_text4: "未付款",
  order_text5: "已支付",
  order_text6: "已退款",
  order_text7: "去付款",
  order_text8: "餘額支付",
  order_text9: "餘額",
  order_text10: "描述",
  order_text11: "金額",
  order_text12: "總額",
  order_text13: "交易日期",
  order_text14: "交易流水",
  order_text15: "暫無資料",
  order_text16: "下載",
  product_text1: "請先完成合約簽訂",
  product_text2: "簽訂",
  regist: "新使用者註冊",
  tip3: "完成註冊後將自動登錄，登入即代表您已同意",
  login_list: "《服務協議與隱私權政策》",
  tip4: "我有銷售",
  tip5: "請輸入銷售編號",
  security_tab1: "SSH金鑰",
  security_tab2: "API日誌",
  security_group: "安全群組",
  create_security_group: "建立安全性群組",
  edit_security_group: "編輯安全組",
  security_label1: "名稱",
  security_btn5: "提交",
  del_group: "刪除安全群組",
  security_label8: "描述",
  security_tips6: "金鑰將用於建立實例時使用，您可以使用您的私鑰登陸雲端伺服器",
  security_tips7: "檢視指南",
  security_btn2: "建立金鑰",
  security_tips8: "編輯",
  security_tips9: "刪除",
  security_title5: "刪除SSH金鑰",
  security_btn10: "建立SSH金鑰",
  security_label7: "公鑰",
  security_title6: "編輯SSH金鑰",
  security_btn1: "建立API",
  security_created_api: "API已創建",
  security_label2: "IP白名單",
  security_text2: "未開啟",
  security_text1: "已開啟",
  security_btn3: "設定",
  security_title2: "建立API",
  security_label9: "私鑰",
  security_label10: "指紋",
  security_btn11: "全部複製",
  security_label4: "建立時間",
  security_tips: "為了確保資料安全，",
  security_tips2: "以上資訊僅在建立時候顯示一次，請務必妥善保存。",
  security_btn8: "我已儲存",
  security_title3: "刪除API",
  security_title4: "IP白名單設定",
  security_tips3: "IP白名單功能可以指定IP位址進行API調用，以確保金鑰安全",
  security_label5: "開啟狀態",
  security_text3: "開",
  security_tips4: "開啟後可指定IP位址進行API呼叫",
  security_label6: "允許存取的IP",
  security_tips5: "請輸入IP位址,每行一段，如：",
  settlement_title: "請核對商品資訊",
  settlement_goodsInfo: "配置詳情",
  settlement_goodsPrice: "單價",
  settlement_goodsNums: "數量",
  settlement_goodsTotalPrice: "小計",
  shoppingCar_tip_text2: "等級折扣金額",
  shoppingCar_tip_text4: "優惠券折扣金額",
  goods_text4: "商品活動折扣金額",
  settlement_tip1: "請選擇付款方式",
  settlement_tip2: "合計",
  shoppingCar_tip_text5: "代金券抵扣金額",
  settlement_tip3: "提交訂單",
  settlement_tip4: "已閱讀並同意",
  settlement_tip6: "和",
  shoppingCar_title: "購物車",
  shoppingCar_tip_text: "請輸入你需要搜尋的內容",
  shoppingCar_editGoods: "修改配置",
  shoppingCar_goodsInfo: "設定詳情",
  shoppingCar_goodsPrice: "單價",
  shoppingCar_goodsNums: "數量",
  shoppingCar_goodsTotalPrice: "小計",
  shoppingCar_goodsAction: "操作",
  shoppingCar_goods_tock_qty: "目前庫存",
  shoppingCar_tock_qty_tip: "因庫存不足，已自動將數量改為目前最大庫存量！",
  shoppingCar_no_goods_tip: "目前商品不存在，請重新購買",
  shoppingCar_buy_again: "重購",
  shoppingCar_no_goods_text: "暫無商品",
  shoppingCar_select_all: "全選",
  shoppingCar_delete_select: "刪除選取的商品",
  shoppingCar_selected: "已選擇",
  shoppingCar_goods_text: "件商品",
  shoppingCar_tip_text3: "合計",
  shoppingCar_buy_text: "結算",
  jump_tip1: "即將離開",
  jump_tip2: "您即將離開",
  jump_tip3: "請注意您的帳號和財產安全。",
  jump_tip4: "繼續訪問",
  subaccount_text54: "官方推播",
  subaccount_text65: "全部訊息",
  subaccount_text68: "請先選擇訊息！",
  oauth_text6: "已關聯",
  oauth_text7: "未關聯",
  account_tips12: "未認證",
  account_tips13: "企業認證",
  account_tips14: "個人認證",
  account_tips25: "請輸入目前密碼",
  account_tips26: "密碼格式錯誤，需為6~32位元的字元",
  account_tips27: "請輸入新密碼",
  account_tips28: "新密碼格式錯誤，需為6~32位元的字元",
  account_tips29: "請輸入驗證密碼",
  account_tips30: "驗證密碼格式錯誤，需為6~32位元的字元",
  account_tips31: "兩次密碼輸入不一致",
  account_tips32: "密碼更改成功！請重新登入",
  account_tips33: "請輸入驗證碼",
  account_tips34: "請輸入6位數驗證碼",
  account_tips35: "手機號碼驗證成功",
  account_tips36: "請輸入新手機號碼",
  account_tips37: "請輸入11位元手機號碼",
  account_tips38: "恭喜您,手機號碼修改成功",
  account_tips39: "郵箱驗證成功",
  ali_tips1: "請輸入信箱",
  account_tips40: "郵箱格式不正確",
  account_tips41: "請輸入信箱驗證碼",
  account_tips42: "郵箱驗證碼應為6位",
  account_tips43: "請輸入手機號碼",
  account_tips44: "請輸入正確的手機號碼",
  account_tips45: "請輸入手機驗證碼",
  account_tips46: "手機驗證碼應為6位",
  account_tips47: "請輸入密碼",
  account_tips48: "請再次輸入密碼",
  account_tips49: "兩次密碼不一致",
  account_tips50: "帳戶資訊",
  template_text46: "請選擇使用者類型",
  template_text47: "請輸入網域名稱擁有者（中文）",
  template_text48: "請輸入聯絡人（中文）",
  template_text49: "請輸入聯絡人姓（中文）",
  template_text50: "請輸入聯絡人名（中文）",
  template_text52: "請選擇地區",
  template_text53: "請輸入郵編",
  template_text54: "請輸入手機號碼",
  template_text55: "請輸入信箱",
  template_text56: "請輸入網域所有者(英文)",
  template_text57: "請輸入網域所有者(英文)",
  template_text58: "請輸入聯絡人姓(英文)",
  template_text59: "請輸入聯絡人名(英文)",
  template_text60: "請輸入通訊位址(中文)",
  template_text61: "請輸入通訊地址(英文)",
  template_text62: "請選擇域名證件類型",
  template_text63: "請輸入域名證件值",
  id_type_SFZ: "身分證",
  id_type_HZ: "護照",
  id_type_GAJMTX: "港澳居民來往內地通行證",
  id_type_TWJMTX: "台灣居民來往大陸通行證",
  id_type_WJLSFZ: "外國人永久居留證",
  id_type_GAJZZ: "港澳台居民居住證明",
  id_type_ORG: "組織機構代碼證",
  id_type_YYZZ: "工商營業執照",
  id_type_TYDM: "統一社會信用代碼證書",
  id_type_BDDM: "部隊代號",
  id_type_JDDWFW: "軍事單位對外有償服務許可證",
  id_type_SYDWFR: "事業單位法人證書",
  id_type_WGCZJG: "外國企業常駐代表機構登記證",
  id_type_SHTTFR: "社會團體法人登記證書",
  id_type_ZJCS: "宗教活動場所登記證",
  id_type_MBFQY: "民辦非企業單位登記證書",
  id_type_JJHFR: "基金會法人登記證書",
  id_type_LSZY: "律師事務所執行許可證",
  id_type_WGZHWH: "外國在華文化中心登記證",
  id_type_WLCZJG: "外國政府旅遊部門常駐代表機構核准登記證",
  id_type_SFJD: "司法鑑定許可證",
  id_type_SHFWJG: "社會服務機構登記證書",
  id_type_MBXXBX: "民辦學校辦學許可證",
  id_type_YLJGZY: "醫療機構執行許可證",
  id_type_JWJG: "境外機構證件",
  id_type_GZJGZY: "公證機構執業證",
  id_type_BJWSXX: "北京市外國駐華使館人員子女學校辦學許可證",
  id_type_QTTYDM: "包含統一社會信用代碼的其它證件",
  template_text64: "請先同意網域名稱資訊服務協議",
  template_text1: "新資訊範本",
  index_text29: "請輸入儲值金額",
  finance_text8: "儲值",
  finance_text9: "扣費",
  finance_text10: "退款",
  finance_text11: "提現",
  finance_text15: "手動訂單",
  finance_text12: "新訂單",
  finance_text13: "續費訂單",
  finance_text14: "升降級訂單",
  finance_text16: "儲值訂單",
  finance_label23: "合併訂單",
  finance_label24: "還款訂單",
  finance_text88: "開通中",
  finance_text89: "使用中",
  finance_text90: "暫停",
  finance_text91: "刪除",
  finance_text92: "開通失敗",
  finance_text93: "已失效",
  finance_text94: "已逾期",
  finance_text95: "生效中",
  finance_text96: "已暫停",
  finance_text97: "待審核",
  finance_text98: "待打款",
  finance_text99: "審核駁回",
  finance_text100: "已打款",
  finance_text101: "未出帳",
  finance_text102: "已出帳",
  finance_text103: "已還款",
  finance_text104: "已逾期",
  finance_text105: "未簽署",
  finance_text106: "審核中",
  finance_text107: "已簽署",
  finance_text108: "待郵寄",
  finance_text109: "已駁回",
  finance_text110: "已作廢",
  finance_text111: "請輸入收件者姓名",
  finance_text112: "請輸入收件者地址",
  finance_text113: "請輸入收件者電話",
  finance_text114: "請輸入甲方名稱",
  finance_text115: "請輸入證件號碼",
  finance_text116: "請輸入聯絡電話",
  finance_text117: "請輸入聯絡信箱",
  finance_text118: "請輸入聯絡地址",
  finance_text119: "銀行卡",
  voucher_effective: "長期有效",
  finance_text120: "請先完成實名認證",
  finance_text121: "等",
  finance_text122: "個商品",
  finance_text123: "財務資訊",
  finance_text124: "申請提現成功",
  finance_text125: "請輸入支付寶帳號",
  finance_text126: "請輸入銀行卡號碼",
  finance_text127: "請輸入銀行卡持有人姓名",
  finance_text128: "請輸入提現金額",
  finance_text129: "提現申請成功",
  finance_text130: "請輸入儲值金額",
  finance_text131: "請選擇儲值方式",
  finance_text132: "付款逾時",
  finance_text133: "刪除成功",
  finance_text134: "請勾選需要合併付款的訂單！",
  template_text137: "文件類型錯誤！",
  template_text138: "至少選擇一個網域購買！",
  template_text139: "請先登入！",
  template_text140: "請輸入網域!",
  template_text141: "最多一次查詢",
  template_text142: "個網域！",
  template_text143: "此網域後綴不支援中文",
  common_cloud_text301: "商城",
  placeholder_pre1: "請輸入",
  security_tip8: "正確的",
  remote_login: "遠端登入和ping",
  web_server: "Web服務",
  database: "資料庫",
  add_cloud_to_group: "將實例新增至安全群組",
  add_cloud_success: "新增實例成功",
  referral_title9: "刪除",
  delete_cloud_success: "解綁安全組成功",
  referral_tips4: "刪除成功！",
  batch_add_rules: "批次新增規則",
  index_text30: "請選擇儲值方式",
  index_text31: "付款逾時",
  index_text33: "首頁",
  index_text34: "詳情",
  index_text32: "複製成功",
  login_text1: "請輸入信箱",
  login_text2: "郵件信箱格式不正確",
  login_text3: "請輸入密碼",
  login_text4: "請輸入信箱驗證碼",
  login_text5: "郵件信箱驗證碼應為6位元",
  login_text6: "請輸入手機號碼",
  login_text7: "請輸入正確的手機號碼",
  account_tips51: "請勾選服務協議書！",
  login_text8: "登入",
  order_text17: "人工",
  order_text18: "充值",
  order_text19: "應用到訂單",
  order_text20: "退款",
  order_text21: "提現",
  order_text22: "訂單詳情",
  order_text23: "餘額",
  login_text9: "註冊",
  account_tips52: "密碼應該在6~32位",
  account_tips53: "請輸入您的銷售編號！",
  security_tips10: "請輸入修改後的名稱",
  security_tips11: "請輸入修改後的公鑰",
  security_tips12: "請輸入名稱",
  security_tips13: "請輸入公鑰",
  shoppingCar_tip_text6: "請先勾選協議後再提交訂單",
  shoppingCar_tip_text7: "商品結算",
  referral_status9: "請先選擇您要刪除的商品",
  referral_status10: "商品庫存不足！",
  referral_status11: "請先選擇您要購買的商品",
  jump_tip: "跳轉提示",
  pay_text17: "複製成功",
  menu_1: "首頁",
  menu_4: "帳戶資訊",
  apply_cashback: "申請返現",
  cashback_tip1: "您可申請的返現金額為",
  cashback_tip2: "申請截止時間至",
  cashback_tip: "*返現金金額將匯入餘額",
  ticket_btn6: "確認",
  shoppingCar_tip_text8: "請選擇代金券",
  shoppingCar_tip_text9: "確定",
  shoppingCar_tip_text10: "使用代金券",
  shoppingCar_tip_text11: "請選擇要使用的代金券！",
  second_try: "秒後再試",
  send_code: "取得驗證碼",
  custom_goods_text3: "請填寫必填資料",
  custom_goods_text1: "必填",
  custom_goods_text2: "格式錯誤",
  login_text10: "請輸入優惠碼",
  shoppingCar_tip_text12: "使用優惠碼",
  shoppingCar_tip_text13: "請輸入優惠碼！",
  shoppingCar_tip_text14: "使用成功！",
  goods_text5: "請選擇活動",
  goods_text6: "不參加活動",
  buy_package: "購買流量包",
  package_tip: "暫無可選流量包",
  login_remember: "記住密碼",
  total: "共",
  pieces: "項資料",
  pay_text1: "訂單提交成功，請盡快付款！訂單號碼",
  pay_text2: "應付金額",
  pay_text3: "可用信用額度",
  pay_text4:
    "提示：您正在使用信用支付，如需退款，請先還清對應週期賬單，否則無法成功退款，請謹慎選擇！",
  pay_text5: "使用餘額付款",
  pay_text6: "當前餘額",
  pay_text7: "線下付款",
  pay_text8: "掃碼付款",
  pay_text9: "確認付款",
  pay_text10: "請離線付款",
  pay_text11: "請掃碼付款",
  pay_text12: "取消",
  pay_text13: "訂單提交成功，請盡快付款！",
  pay_text14: "應付",
  pay_text15: "訂單號碼",
  pay_text16: "信用額度不夠！",
  pay_text18: "信用支付",
  pay_text19: "使用餘額付款",
  pay_text20: "付款逾時",
  subaccount_text57: "看更多",
  subaccount_text55: "暫無訊息",
  topMenu_text1: "登入/註冊",
  topMenu_text2: "帳號資訊",
  topMenu_text3: "退出登入",
  topMenu_text4: "您將登出登錄，是否繼續",
  topMenu_text5: "提示",
  topMenu_text6: "確定",
  topMenu_text7: "取消",
  withdraw_title: "申請提現",
  withdraw_label1: "提現方式",
  withdraw_placeholder1: "請選擇提現方式",
  withdraw_label2: "銀行卡號",
  withdraw_placeholder3: "請輸入銀行卡號碼",
  withdraw_label3: "帳號",
  withdraw_placeholder2: "請輸入帳號",
  withdraw_label4: "姓名",
  withdraw_placeholder4: "請輸入姓名",
  withdraw_label5: "提現金金額",
  withdraw_placeholder5: "可提現",
  withdraw_btn3: "全部",
  withdraw_title2: "提現規則",
  withdraw_text1: "•單次提現",
  withdraw_text2: "不能低於",
  withdraw_text3: "不能超過",
  withdraw_text4: "•提現手續費：每次",
  withdraw_text5: "最低",
  withdraw_text6: "(用於發票稅點及增值稅點繳納等)",
  withdraw_text7: "•需通過平台實名認證",
  withdraw_btn1: "提交",
  withdraw_btn2: "取消",
  withdraw_placeholder6: "請輸入提現金額",
  withdraw_tips1: "提現金金額不能小於",
  withdraw_tips2: "提現金金額超出可提現金額",
  withdraw_tips3: "提現金金額不能大於",
  buy_tip_text: "请完善必填信息后购买",

  product_list_status1: "使用中",
  product_list_status2: "即將到期",
  product_list_status3: "已逾期",
  product_list_status4: "已删除",
  product_conig_tip: "請聯絡管理員以完善商品配置",
  cart_tip_text1: "該商品需與下列商品捆綁購買",
  cart_tip_text2: "去訂購",
  cart_tip_text3: "操作",
  cart_tip_text4: "商品名稱",
  cart_tip_text5: "購物車已有該商品",
  cart_tip_text6: "捆綁商品建議加入購物車後統一結算",
  cart_tip_text7: "大量續約",
  cart_tip_text8: "請選擇需要續費的產品",
  cart_tip_text9: "確認",
  cart_tip_text10: "取消",
  cart_tip_text11: "產品名稱",
  cart_tip_text12: "週期",
  cart_tip_text13: "金額",
  cart_tip_text14: "綁定商品數量不一致，請檢查後再結算",
  cart_tip_text15: "確認進行",
  cart_tip_text16: "批次開機",
  cart_tip_text17: "批次關機",
  cart_tip_text18: "批次重啟",
  cart_tip_text19: "批次強制關機",
  cart_tip_text20: "批次強制重啟",
  cart_tip_text21: "取消關聯",
  cart_tip_text22: "批次操作",
  cart_tip_text23: "請選擇需要的批次操作",
  cart_tip_text24: "請選擇要操作的產品",
  cart_tip_text25: "結果",
  cart_tip_text26: "操作成功",

  account_tips_text1: "目前資訊暫時無法變更，請聯絡管理員處理",
  account_tips_text2: "請輸入操作密碼",
  account_tips_text3: "操作密碼",
  account_tips_text4: "更改操作密碼",
  account_tips_text5: "設定操作密碼",
  account_tips_text6: "請先完善",
  account_tips_text7:
    "系統偵測到您此次登入異常，為了確保帳戶安全，請核驗您的身分",
  account_tips_text8: "請妥善保管密碼，若遺忘密碼請聯絡管理員處理",
  account_tips_text9: "是否接收定時通知",
  account_tips_text10: "簡訊",
  account_tips_text11: "郵件",
  account_tips_text12: "全部",
  account_tips_text13: "未來15分鐘，不再驗證操作密碼(退出登錄失效)",

  finance_credit1: "授信金額已失效",
  finance_credit2: "提前還款",
  finance_credit3: "確認提前還款？",
  finance_credit4: "賬單周期",
  finance_credit5: "提前還款金額",
  finance_credit6: "提前還款訂單超過壹天未支付將自動刪除",
  finance_info: "產品信息",

  pay_text21: "余額組合支付",
  pay_text22: "提交申請",
  finance_custom1: "待上傳",
  finance_custom2: "待審核",
  finance_custom3: "未通過",
  finance_custom4: "上傳憑證",
  finance_custom5: "重新上傳",
  finance_custom6: "訂單ID",
  finance_custom7: "提交申請",
  finance_custom8: "管理員審核",
  finance_custom9: "審核不通過",
  finance_custom10: "購買成功",
  finance_custom11: "還需支付",
  finance_custom12: "余額已抵扣",
  finance_custom13: "請上傳憑證",
  finance_custom14: "變更支付方式",
  finance_custom15: "是否變更支付方式？",
  finance_custom16: "將文件拖到此處或",
  finance_custom17: "點擊上傳",
  finance_custom18: "最多上傳10個文件，限制圖片或PDF格式。",
  finance_custom19: "查看憑證",
  finance_custom20: "提交轉賬憑證",

  finance_custom21: "產品標識",
  finance_custom22: "交易記錄",

  finance_custom23: "企業認證ID",
  finance_custom24: "個人認證ID",
  finance_custom25: "複製",

  wx_tip1: "請用微信掃碼關注公眾號接收微信通知",
  wx_tip2: "微信公眾號訊息推播",
  wx_tip3: "允許公眾號碼訊息推送",
  firewall_text1: "支持防火牆",
  host_transferring: "轉移中",

  /* 通用自動續費 */
  auto_renew: "自動續費",
  auto_renew_sure: "確定",
  auto_renew_cancel: "取消",
  auto_renew_name: "產品名稱",
  auto_renew_host: "主機名稱",
  auto_renew_area: "區域",
  auto_renew_due: "到期時間",
  auto_renew_cycle: "續費金額/週期",
  auto_renew_tip1: "請確認您將為以下產品",
  auto_renew_tip2: "開啟自動續費",
  auto_renew_tip3: "關閉自動續費",
  stock: "庫存",
};

window.lang = lang_obj;
