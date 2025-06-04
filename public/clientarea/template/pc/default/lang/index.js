(function () {
  const Element_lang = {
    "en-us": "en",
    "zh-cn": "zhCN",
    "zh-hk": "zhTW",
  };
  let DEFAULT_LANG;
  if (localStorage.getItem("jwt") && localStorage.getItem("lang")) {
    DEFAULT_LANG = localStorage.getItem("lang");
  } else {
    DEFAULT_LANG = getBrowserLanguage();
    localStorage.setItem("lang", DEFAULT_LANG);
  }
  document.writeln(
    `<script src="${url}lang/${DEFAULT_LANG}/element-lang.js"><\/script>`
  );
  document.writeln(
    `<script src="${url}lang/${DEFAULT_LANG}/index.js?v=${system_version}"><\/script>`
  );
  document.writeln(
    `<script>ELEMENT.locale(ELEMENT.lang.${Element_lang[DEFAULT_LANG]})<\/script>`
  );
})();
