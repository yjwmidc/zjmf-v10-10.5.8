!(function (n, t) {
  "object" == typeof exports && "undefined" != typeof module
    ? t(exports)
    : "function" == typeof define && define.amd
    ? define(["exports"], t)
    : t(
        ((n =
          "undefined" != typeof globalThis
            ? globalThis
            : n || self).VueCompositionAPI = {})
      );
})(this, function (n) {
  "use strict";
  var t = function (n, e) {
    return (
      (t =
        Object.setPrototypeOf ||
        ({__proto__: []} instanceof Array &&
          function (n, t) {
            n.__proto__ = t;
          }) ||
        function (n, t) {
          for (var e in t)
            Object.prototype.hasOwnProperty.call(t, e) && (n[e] = t[e]);
        }),
      t(n, e)
    );
  };
  var e,
    r = function () {
      return (
        (r =
          Object.assign ||
          function (n) {
            for (var t, e = 1, r = arguments.length; e < r; e++)
              for (var o in (t = arguments[e]))
                Object.prototype.hasOwnProperty.call(t, o) && (n[o] = t[o]);
            return n;
          }),
        r.apply(this, arguments)
      );
    };
  function o(n) {
    var t = "function" == typeof Symbol && Symbol.iterator,
      e = t && n[t],
      r = 0;
    if (e) return e.call(n);
    if (n && "number" == typeof n.length)
      return {
        next: function () {
          return (
            n && r >= n.length && (n = void 0), {value: n && n[r++], done: !n}
          );
        },
      };
    throw new TypeError(
      t ? "Object is not iterable." : "Symbol.iterator is not defined."
    );
  }
  function i(n, t) {
    var e = "function" == typeof Symbol && n[Symbol.iterator];
    if (!e) return n;
    var r,
      o,
      i = e.call(n),
      u = [];
    try {
      for (; (void 0 === t || t-- > 0) && !(r = i.next()).done; )
        u.push(r.value);
    } catch (n) {
      o = {error: n};
    } finally {
      try {
        r && !r.done && (e = i.return) && e.call(i);
      } finally {
        if (o) throw o.error;
      }
    }
    return u;
  }
  function u(n, t, e) {
    if (e || 2 === arguments.length)
      for (var r, o = 0, i = t.length; o < i; o++)
        (!r && o in t) ||
          (r || (r = Array.prototype.slice.call(t, 0, o)), (r[o] = t[o]));
    return n.concat(r || Array.prototype.slice.call(t));
  }
  var f = [],
    a = (function () {
      function n(n) {
        (this.active = !0),
          (this.effects = []),
          (this.cleanups = []),
          (this.vm = n);
      }
      return (
        (n.prototype.run = function (n) {
          if (this.active)
            try {
              return this.on(), n();
            } finally {
              this.off();
            }
        }),
        (n.prototype.on = function () {
          this.active && (f.push(this), (e = this));
        }),
        (n.prototype.off = function () {
          this.active && (f.pop(), (e = f[f.length - 1]));
        }),
        (n.prototype.stop = function () {
          this.active &&
            (this.vm.$destroy(),
            this.effects.forEach(function (n) {
              return n.stop();
            }),
            this.cleanups.forEach(function (n) {
              return n();
            }),
            (this.active = !1));
        }),
        n
      );
    })(),
    c = (function (n) {
      function r(t) {
        void 0 === t && (t = !1);
        var r,
          o = void 0;
        return (
          (function (n) {
            var t = _;
            _ = !1;
            try {
              n();
            } finally {
              _ = t;
            }
          })(function () {
            o = z(g());
          }),
          (r = n.call(this, o) || this),
          t ||
            (function (n, t) {
              var r;
              if ((t = t || e) && t.active) return void t.effects.push(n);
              var o = null === (r = $()) || void 0 === r ? void 0 : r.proxy;
              o &&
                o.$on("hook:destroyed", function () {
                  return n.stop();
                });
            })(r),
          r
        );
      }
      return (
        (function (n, e) {
          if ("function" != typeof e && null !== e)
            throw new TypeError(
              "Class extends value " +
                String(e) +
                " is not a constructor or null"
            );
          function r() {
            this.constructor = n;
          }
          t(n, e),
            (n.prototype =
              null === e
                ? Object.create(e)
                : ((r.prototype = e.prototype), new r()));
        })(r, n),
        r
      );
    })(a);
  function l() {
    return e;
  }
  function s() {
    var n, t;
    return (
      (null === (n = l()) || void 0 === n ? void 0 : n.vm) ||
      (null === (t = $()) || void 0 === t ? void 0 : t.proxy)
    );
  }
  var v = void 0;
  try {
    var d = require("vue");
    d && b(d)
      ? (v = d)
      : d && "default" in d && b(d.default) && (v = d.default);
  } catch (n) {}
  var p = null,
    y = null,
    _ = !0,
    h = "__composition_api_installed__";
  function b(n) {
    return n && V(n) && "Vue" === n.name;
  }
  function g() {
    return p;
  }
  function m() {
    return p || v;
  }
  function w(n) {
    if (_) {
      var t = y;
      null == t || t.scope.off(), null == (y = n) || y.scope.on();
    }
  }
  function $() {
    return y;
  }
  var j = new WeakMap();
  function x(n) {
    if (j.has(n)) return j.get(n);
    var t = {
      proxy: n,
      update: n.$forceUpdate,
      type: n.$options,
      uid: n._uid,
      emit: n.$emit.bind(n),
      parent: null,
      root: null,
    };
    !(function (n) {
      if (!n.scope) {
        var t = new a(n.proxy);
        (n.scope = t),
          n.proxy.$on("hook:destroyed", function () {
            return t.stop();
          });
      }
      n.scope;
    })(t);
    return (
      ["data", "props", "attrs", "refs", "vnode", "slots"].forEach(function (
        e
      ) {
        E(t, e, {
          get: function () {
            return n["$".concat(e)];
          },
        });
      }),
      E(t, "isMounted", {
        get: function () {
          return n._isMounted;
        },
      }),
      E(t, "isUnmounted", {
        get: function () {
          return n._isDestroyed;
        },
      }),
      E(t, "isDeactivated", {
        get: function () {
          return n._inactive;
        },
      }),
      E(t, "emitted", {
        get: function () {
          return n._events;
        },
      }),
      j.set(n, t),
      n.$parent && (t.parent = x(n.$parent)),
      n.$root && (t.root = x(n.$root)),
      t
    );
  }
  function O(n) {
    return "function" == typeof n && /native code/.test(n.toString());
  }
  var S =
      "undefined" != typeof Symbol &&
      O(Symbol) &&
      "undefined" != typeof Reflect &&
      O(Reflect.ownKeys),
    k = function (n) {
      return n;
    };
  function E(n, t, e) {
    var r = e.get,
      o = e.set;
    Object.defineProperty(n, t, {
      enumerable: !0,
      configurable: !0,
      get: r || k,
      set: o || k,
    });
  }
  function R(n, t, e, r) {
    Object.defineProperty(n, t, {
      value: e,
      enumerable: !!r,
      writable: !0,
      configurable: !0,
    });
  }
  function C(n, t) {
    return Object.hasOwnProperty.call(n, t);
  }
  function M(n) {
    return Array.isArray(n);
  }
  var P,
    D = Object.prototype.toString,
    A = function (n) {
      return D.call(n);
    };
  function U(n) {
    var t = parseFloat(String(n));
    return t >= 0 && Math.floor(t) === t && isFinite(n) && t <= 4294967295;
  }
  function B(n) {
    return null !== n && "object" == typeof n;
  }
  function T(n) {
    return (
      "[object Object]" ===
      (function (n) {
        return Object.prototype.toString.call(n);
      })(n)
    );
  }
  function V(n) {
    return "function" == typeof n;
  }
  function W(n, t) {
    return (t = t || $());
  }
  function z(n, t) {
    void 0 === t && (t = {});
    var e = n.config.silent;
    n.config.silent = !0;
    var r = new n(t);
    return (n.config.silent = e), r;
  }
  function F(n, t) {
    return function () {
      for (var e = [], r = 0; r < arguments.length; r++) e[r] = arguments[r];
      if (n.$scopedSlots[t]) return n.$scopedSlots[t].apply(n, e);
    };
  }
  function I(n) {
    return S ? Symbol.for(n) : n;
  }
  var K = I("composition-api.preFlushQueue"),
    Q = I("composition-api.postFlushQueue"),
    q = "composition-api.refKey",
    G = new WeakMap(),
    H = new WeakMap(),
    J = new WeakMap();
  function L(n, t, e) {
    var r = g().util;
    r.warn;
    var o = r.defineReactive,
      i = n.__ob__;
    function u() {
      i && B(e) && !C(e, "__ob__") && sn(e);
    }
    if (M(n)) {
      if (U(t))
        return (n.length = Math.max(n.length, t)), n.splice(t, 1, e), u(), e;
      if ("length" === t && e !== n.length)
        return (n.length = e), null == i || i.dep.notify(), e;
    }
    return t in n && !(t in Object.prototype)
      ? ((n[t] = e), u(), e)
      : n._isVue || (i && i.vmCount)
      ? e
      : i
      ? (o(i.value, t, e), cn(n, t, e), u(), i.dep.notify(), e)
      : ((n[t] = e), e);
  }
  var N = !1;
  function X(n) {
    N = n;
  }
  var Y = function (n) {
    E(this, "value", {get: n.get, set: n.set});
  };
  function Z(n, t, e) {
    void 0 === t && (t = !1), void 0 === e && (e = !1);
    var r = new Y(n);
    e && (r.effect = !0);
    var o = Object.seal(r);
    return t && J.set(o, !0), o;
  }
  function nn(n) {
    var t;
    if (tn(n)) return n;
    var e = pn((((t = {})[q] = n), t));
    return Z({
      get: function () {
        return e[q];
      },
      set: function (n) {
        return (e[q] = n);
      },
    });
  }
  function tn(n) {
    return n instanceof Y;
  }
  function en(n) {
    return tn(n) ? n.value : n;
  }
  function rn(n) {
    if (!T(n)) return n;
    var t = {};
    for (var e in n) t[e] = on(n, e);
    return t;
  }
  function on(n, t) {
    t in n || L(n, t, void 0);
    var e = n[t];
    return tn(e)
      ? e
      : Z({
          get: function () {
            return n[t];
          },
          set: function (e) {
            return (n[t] = e);
          },
        });
  }
  function un(n) {
    var t;
    return Boolean(
      n &&
        C(n, "__ob__") &&
        "object" == typeof n.__ob__ &&
        (null === (t = n.__ob__) || void 0 === t ? void 0 : t.__v_skip)
    );
  }
  function fn(n) {
    var t;
    return Boolean(
      n &&
        C(n, "__ob__") &&
        "object" == typeof n.__ob__ &&
        !(null === (t = n.__ob__) || void 0 === t ? void 0 : t.__v_skip)
    );
  }
  function an(n) {
    if (
      !(
        !T(n) ||
        un(n) ||
        M(n) ||
        tn(n) ||
        ((t = n), (e = g()), e && t instanceof e) ||
        G.has(n)
      )
    ) {
      var t, e;
      G.set(n, !0);
      for (var r = Object.keys(n), o = 0; o < r.length; o++) cn(n, r[o]);
    }
  }
  function cn(n, t, e) {
    if ("__ob__" !== t && !un(n[t])) {
      var r,
        o,
        i = Object.getOwnPropertyDescriptor(n, t);
      if (i) {
        if (!1 === i.configurable) return;
        (r = i.get),
          (o = i.set),
          (r && !o) || 2 !== arguments.length || (e = n[t]);
      }
      an(e),
        E(n, t, {
          get: function () {
            var o = r ? r.call(n) : e;
            return t !== q && tn(o) ? o.value : o;
          },
          set: function (i) {
            (r && !o) ||
              (t !== q && tn(e) && !tn(i)
                ? (e.value = i)
                : o
                ? (o.call(n, i), (e = i))
                : (e = i),
              an(i));
          },
        });
    }
  }
  function ln(n) {
    var t,
      e = m();
    e.observable
      ? (t = e.observable(n))
      : (t = z(e, {data: {$$state: n}})._data.$$state);
    return C(t, "__ob__") || sn(t), t;
  }
  function sn(n, t) {
    var e, r;
    if (
      (void 0 === t && (t = new Set()),
      !t.has(n) && !C(n, "__ob__") && Object.isExtensible(n))
    ) {
      R(
        n,
        "__ob__",
        (function (n) {
          void 0 === n && (n = {});
          return {
            value: n,
            dep: {notify: k, depend: k, addSub: k, removeSub: k},
          };
        })(n)
      ),
        t.add(n);
      try {
        for (var i = o(Object.keys(n)), u = i.next(); !u.done; u = i.next()) {
          var f = n[u.value];
          (T(f) || M(f)) && !un(f) && Object.isExtensible(f) && sn(f, t);
        }
      } catch (n) {
        e = {error: n};
      } finally {
        try {
          u && !u.done && (r = i.return) && r.call(i);
        } finally {
          if (e) throw e.error;
        }
      }
    }
  }
  function vn() {
    return ln({}).__ob__;
  }
  function dn(n) {
    var t, e;
    if (!B(n)) return n;
    if ((!T(n) && !M(n)) || un(n) || !Object.isExtensible(n)) return n;
    var r = ln(M(n) ? [] : {}),
      i = r.__ob__,
      u = function (t) {
        var e,
          o,
          u = n[t],
          f = Object.getOwnPropertyDescriptor(n, t);
        if (f) {
          if (!1 === f.configurable) return "continue";
          (e = f.get), (o = f.set);
        }
        E(r, t, {
          get: function () {
            var n;
            return null === (n = i.dep) || void 0 === n || n.depend(), u;
          },
          set: function (t) {
            var r;
            (e && !o) ||
              ((N || u !== t) &&
                (o ? o.call(n, t) : (u = t),
                null === (r = i.dep) || void 0 === r || r.notify()));
          },
        });
      };
    try {
      for (var f = o(Object.keys(n)), a = f.next(); !a.done; a = f.next()) {
        u(a.value);
      }
    } catch (n) {
      t = {error: n};
    } finally {
      try {
        a && !a.done && (e = f.return) && e.call(f);
      } finally {
        if (t) throw t.error;
      }
    }
    return r;
  }
  function pn(n) {
    if (!B(n)) return n;
    if ((!T(n) && !M(n)) || un(n) || !Object.isExtensible(n)) return n;
    var t = ln(n);
    return an(t), t;
  }
  function yn(n) {
    return function (t, e) {
      var r,
        o = W("on".concat((r = n)[0].toUpperCase() + r.slice(1)), e);
      return (
        o &&
        (function (n, t, e, r) {
          var o = t.proxy.$options,
            f = n.config.optionMergeStrategies[e],
            a = (function (n, t) {
              return function () {
                for (var e = [], r = 0; r < arguments.length; r++)
                  e[r] = arguments[r];
                var o = $();
                w(n);
                try {
                  return t.apply(void 0, u([], i(e), !1));
                } finally {
                  w(o);
                }
              };
            })(t, r);
          return (o[e] = f(o[e], a)), a;
        })(g(), o, n, t)
      );
    };
  }
  var _n,
    hn = yn("beforeMount"),
    bn = yn("mounted"),
    gn = yn("beforeUpdate"),
    mn = yn("updated"),
    wn = yn("beforeDestroy"),
    $n = yn("destroyed"),
    jn = yn("errorCaptured"),
    xn = yn("activated"),
    On = yn("deactivated"),
    Sn = yn("serverPrefetch");
  function kn() {
    Cn(this, K);
  }
  function En() {
    Cn(this, Q);
  }
  function Rn() {
    var n = s();
    return (
      n
        ? (function (n) {
            return void 0 !== n[K];
          })(n) ||
          (function (n) {
            (n[K] = []),
              (n[Q] = []),
              n.$on("hook:beforeUpdate", kn),
              n.$on("hook:updated", En);
          })(n)
        : (_n || (_n = z(g())), (n = _n)),
      n
    );
  }
  function Cn(n, t) {
    for (var e = n[t], r = 0; r < e.length; r++) e[r]();
    e.length = 0;
  }
  function Mn(n, t, e) {
    var r = function () {
      n.$nextTick(function () {
        n[K].length && Cn(n, K), n[Q].length && Cn(n, Q);
      });
    };
    switch (e) {
      case "pre":
        r(), n[K].push(t);
        break;
      case "post":
        r(), n[Q].push(t);
        break;
      default:
        !(function (n, t) {
          if (!n) throw new Error("[vue-composition-api] ".concat(t));
        })(
          !1,
          'flush must be one of ["post", "pre", "sync"], but got '.concat(e)
        );
    }
  }
  function Pn(n, t) {
    var e = n.teardown;
    n.teardown = function () {
      for (var r = [], o = 0; o < arguments.length; o++) r[o] = arguments[o];
      e.apply(n, r), t();
    };
  }
  function Dn(n, t, e, r) {
    var o,
      f,
      a = r.flush,
      c = "sync" === a,
      l = function (n) {
        f = function () {
          try {
            n();
          } catch (n) {
            !(function (n, t, e) {
              if ("undefined" == typeof window || "undefined" == typeof console)
                throw n;
              console.error(n);
            })(n);
          }
        };
      },
      s = function () {
        f && (f(), (f = null));
      },
      v = function (t) {
        return c || n === _n
          ? t
          : function () {
              for (var e = [], r = 0; r < arguments.length; r++)
                e[r] = arguments[r];
              return Mn(
                n,
                function () {
                  t.apply(void 0, u([], i(e), !1));
                },
                a
              );
            };
      };
    if (null === e) {
      var d = !1,
        p = (function (n, t, e, r) {
          var o = n._watchers.length;
          return (
            n.$watch(t, e, {
              immediate: r.immediateInvokeCallback,
              deep: r.deep,
              lazy: r.noRun,
              sync: r.sync,
              before: r.before,
            }),
            n._watchers[o]
          );
        })(
          n,
          function () {
            if (!d)
              try {
                (d = !0), t(l);
              } finally {
                d = !1;
              }
          },
          k,
          {deep: r.deep || !1, sync: c, before: s}
        );
      Pn(p, s), (p.lazy = !1);
      var y = p.get.bind(p);
      return (
        (p.get = v(y)),
        function () {
          p.teardown();
        }
      );
    }
    var _,
      h = r.deep,
      b = !1;
    if (
      (tn(t)
        ? (_ = function () {
            return t.value;
          })
        : fn(t)
        ? ((_ = function () {
            return t;
          }),
          (h = !0))
        : M(t)
        ? ((b = !0),
          (_ = function () {
            return t.map(function (n) {
              return tn(n) ? n.value : fn(n) ? Un(n) : V(n) ? n() : k;
            });
          }))
        : (_ = V(t) ? t : k),
      h)
    ) {
      var g = _;
      _ = function () {
        return Un(g());
      };
    }
    var m = function (n, t) {
        if (
          h ||
          !b ||
          !n.every(function (n, e) {
            return (
              (r = n),
              (o = t[e]),
              r === o ? 0 !== r || 1 / r == 1 / o : r != r && o != o
            );
            var r, o;
          })
        )
          return s(), e(n, t, l);
      },
      w = v(m);
    if (r.immediate) {
      var $ = w,
        j = function (n, t) {
          return (j = $), m(n, M(n) ? [] : t);
        };
      w = function (n, t) {
        return j(n, t);
      };
    }
    var x = n.$watch(_, w, {immediate: r.immediate, deep: h, sync: c}),
      O = n._watchers[n._watchers.length - 1];
    return (
      fn(O.value) &&
        (null === (o = O.value.__ob__) || void 0 === o ? void 0 : o.dep) &&
        h &&
        O.value.__ob__.dep.addSub({
          update: function () {
            O.run();
          },
        }),
      Pn(O, s),
      function () {
        x();
      }
    );
  }
  function An(n, t) {
    var e = (function (n) {
      return r({flush: "pre"}, n);
    })(t);
    return Dn(Rn(), n, null, e);
  }
  function Un(n, t) {
    if ((void 0 === t && (t = new Set()), !B(n) || t.has(n) || H.has(n)))
      return n;
    if ((t.add(n), tn(n))) Un(n.value, t);
    else if (M(n)) for (var e = 0; e < n.length; e++) Un(n[e], t);
    else if (
      "[object Set]" === A(n) ||
      (function (n) {
        return "[object Map]" === A(n);
      })(n)
    )
      n.forEach(function (n) {
        Un(n, t);
      });
    else if (T(n)) for (var r in n) Un(n[r], t);
    return n;
  }
  var Bn = {};
  function Tn(n, t) {
    for (var e = t; e; ) {
      if (e._provided && C(e._provided, n)) return e._provided[n];
      e = e.$parent;
    }
    return Bn;
  }
  var Vn = {},
    Wn = function (n) {
      var t;
      void 0 === n && (n = "$style");
      var e = $();
      if (!e) return Vn;
      var r = null === (t = e.proxy) || void 0 === t ? void 0 : t[n];
      return r || Vn;
    },
    zn = Wn;
  var Fn;
  function In() {
    return $().setupContext;
  }
  var Kn = {
    set: function (n, t, e) {
      (n.__composition_api_state__ = n.__composition_api_state__ || {})[t] = e;
    },
    get: function (n, t) {
      return (n.__composition_api_state__ || {})[t];
    },
  };
  function Qn(n) {
    var t = Kn.get(n, "rawBindings") || {};
    if (t && Object.keys(t).length) {
      for (
        var e = n.$refs, r = Kn.get(n, "refs") || [], o = 0;
        o < r.length;
        o++
      ) {
        var i = t[(a = r[o])];
        !e[a] && i && tn(i) && (i.value = null);
      }
      var u = Object.keys(e),
        f = [];
      for (o = 0; o < u.length; o++) {
        var a;
        i = t[(a = u[o])];
        e[a] && i && tn(i) && ((i.value = e[a]), f.push(a));
      }
      Kn.set(n, "refs", f);
    }
  }
  function qn(n) {
    for (var t = [n._vnode]; t.length; ) {
      var e = t.pop();
      if (e && (e.context && Qn(e.context), e.children))
        for (var r = 0; r < e.children.length; ++r) t.push(e.children[r]);
    }
  }
  function Gn(n, t) {
    var e, r;
    if (n) {
      var i = Kn.get(n, "attrBindings");
      if (i || t) {
        if (!i) {
          var u = pn({});
          (i = {ctx: t, data: u}),
            Kn.set(n, "attrBindings", i),
            E(t, "attrs", {
              get: function () {
                return null == i ? void 0 : i.data;
              },
              set: function () {},
            });
        }
        var f = n.$attrs,
          a = function (t) {
            C(i.data, t) ||
              E(i.data, t, {
                get: function () {
                  return n.$attrs[t];
                },
              });
          };
        try {
          for (var c = o(Object.keys(f)), l = c.next(); !l.done; l = c.next()) {
            a(l.value);
          }
        } catch (n) {
          e = {error: n};
        } finally {
          try {
            l && !l.done && (r = c.return) && r.call(c);
          } finally {
            if (e) throw e.error;
          }
        }
      }
    }
  }
  function Hn(n, t) {
    var e = n.$options._parentVnode;
    if (e) {
      for (
        var r = Kn.get(n, "slots") || [],
          o = (function (n, t) {
            var e;
            if (n) {
              if (n._normalized) return n._normalized;
              for (var r in ((e = {}), n)) n[r] && "$" !== r[0] && (e[r] = !0);
            } else e = {};
            for (var r in t) (r in e) || (e[r] = !0);
            return e;
          })(e.data.scopedSlots, n.$slots),
          i = 0;
        i < r.length;
        i++
      ) {
        o[(f = r[i])] || delete t[f];
      }
      var u = Object.keys(o);
      for (i = 0; i < u.length; i++) {
        var f;
        t[(f = u[i])] || (t[f] = F(n, f));
      }
      Kn.set(n, "slots", u);
    }
  }
  function Jn(n, t, e) {
    var r = $();
    w(n);
    try {
      return t(n);
    } catch (n) {
      if (!e) throw n;
      e(n);
    } finally {
      w(r);
    }
  }
  function Ln(n) {
    function t(n, e) {
      if (
        (void 0 === e && (e = new Set()),
        !e.has(n) && T(n) && !tn(n) && !fn(n) && !un(n))
      ) {
        var r = g().util.defineReactive;
        Object.keys(n).forEach(function (o) {
          var i = n[o];
          r(n, o, i), i && (e.add(i), t(i, e));
        });
      }
    }
    function e(n, t) {
      return (
        void 0 === t && (t = new Map()),
        t.has(n)
          ? t.get(n)
          : (t.set(n, !1),
            M(n) && fn(n)
              ? (t.set(n, !0), !0)
              : !(!T(n) || un(n) || tn(n)) &&
                Object.keys(n).some(function (r) {
                  return e(n[r], t);
                }))
      );
    }
    n.mixin({
      beforeCreate: function () {
        var n = this,
          r = n.$options,
          o = r.setup,
          i = r.render;
        i &&
          (r.render = function () {
            for (var t = this, e = [], r = 0; r < arguments.length; r++)
              e[r] = arguments[r];
            return Jn(x(n), function () {
              return i.apply(t, e);
            });
          });
        if (!o) return;
        if (!V(o)) return;
        var u = r.data;
        r.data = function () {
          return (
            (function (n, r) {
              void 0 === r && (r = {});
              var o,
                i = n.$options.setup,
                u = (function (n) {
                  var t = {slots: {}},
                    e = ["emit"];
                  return (
                    [
                      "root",
                      "parent",
                      "refs",
                      "listeners",
                      "isServer",
                      "ssrContext",
                    ].forEach(function (e) {
                      var r = "$".concat(e);
                      E(t, e, {
                        get: function () {
                          return n[r];
                        },
                        set: function () {},
                      });
                    }),
                    Gn(n, t),
                    e.forEach(function (e) {
                      var r = "$".concat(e);
                      E(t, e, {
                        get: function () {
                          return function () {
                            for (var t = [], e = 0; e < arguments.length; e++)
                              t[e] = arguments[e];
                            n[r].apply(n, t);
                          };
                        },
                      });
                    }),
                    t
                  );
                })(n),
                f = x(n);
              if (
                ((f.setupContext = u),
                R(r, "__ob__", vn()),
                Hn(n, u.slots),
                Jn(f, function () {
                  o = i(r, u);
                }),
                !o)
              )
                return;
              if (V(o)) {
                var a = o;
                return void (n.$options.render = function () {
                  return (
                    Hn(n, u.slots),
                    Jn(f, function () {
                      return a();
                    })
                  );
                });
              }
              if (B(o)) {
                fn(o) && (o = rn(o)), Kn.set(n, "rawBindings", o);
                var c = o;
                Object.keys(c).forEach(function (r) {
                  var o = c[r];
                  if (!tn(o))
                    if (fn(o)) M(o) && (o = nn(o));
                    else if (V(o)) {
                      var i = o;
                      (o = o.bind(n)),
                        Object.keys(i).forEach(function (n) {
                          o[n] = i[n];
                        });
                    } else B(o) ? e(o) && t(o) : (o = nn(o));
                  !(function (n, t, e) {
                    var r = n.$options.props;
                    t in n ||
                      (r && C(r, t)) ||
                      (tn(e)
                        ? E(n, t, {
                            get: function () {
                              return e.value;
                            },
                            set: function (n) {
                              e.value = n;
                            },
                          })
                        : E(n, t, {
                            get: function () {
                              return fn(e) && e.__ob__.dep.depend(), e;
                            },
                            set: function (n) {
                              e = n;
                            },
                          }));
                  })(n, r, o);
                });
              }
            })(n, n.$props),
            V(u) ? u.call(n, n) : u || {}
          );
        };
      },
      mounted: function () {
        qn(this);
      },
      beforeUpdate: function () {
        Gn(this);
      },
      updated: function () {
        qn(this);
      },
    });
  }
  function Nn(n, t) {
    if (!n) return t;
    if (!t) return n;
    for (
      var e, r, o, i = S ? Reflect.ownKeys(n) : Object.keys(n), u = 0;
      u < i.length;
      u++
    )
      "__ob__" !== (e = i[u]) &&
        ((r = t[e]),
        (o = n[e]),
        C(t, e)
          ? r !== o && T(r) && !tn(r) && T(o) && !tn(o) && Nn(o, r)
          : (t[e] = o));
    return t;
  }
  function Xn(n) {
    (function (n) {
      return p && C(n, h);
    })(n) ||
      ((n.config.optionMergeStrategies.setup = function (n, t) {
        return function (e, r) {
          return Nn(
            V(n) ? n(e, r) || {} : void 0,
            V(t) ? t(e, r) || {} : void 0
          );
        };
      }),
      (function (n) {
        (p = n),
          Object.defineProperty(n, h, {
            configurable: !0,
            writable: !0,
            value: !0,
          });
      })(n),
      Ln(n));
  }
  var Yn = {
    install: function (n) {
      return Xn(n);
    },
  };
  "undefined" != typeof window && window.Vue && window.Vue.use(Yn),
    (n.EffectScope = c),
    (n.computed = function (n) {
      var t,
        e,
        r,
        o,
        i = s();
      if ((V(n) ? (t = n) : ((t = n.get), (e = n.set)), i && !i.$isServer)) {
        var u,
          f = (function () {
            if (!P) {
              var n = z(g(), {
                  computed: {
                    value: function () {
                      return 0;
                    },
                  },
                }),
                t = n._computedWatchers.value.constructor,
                e = n._data.__ob__.dep.constructor;
              (P = {Watcher: t, Dep: e}), n.$destroy();
            }
            return P;
          })(),
          a = f.Watcher,
          c = f.Dep;
        (o = function () {
          return (
            u || (u = new a(i, t, k, {lazy: !0})),
            u.dirty && u.evaluate(),
            c.target && u.depend(),
            u.value
          );
        }),
          (r = function (n) {
            e && e(n);
          });
      } else {
        var l = z(g(), {computed: {$$state: {get: t, set: e}}});
        i &&
          i.$on("hook:destroyed", function () {
            return l.$destroy();
          }),
          (o = function () {
            return l.$$state;
          }),
          (r = function (n) {
            l.$$state = n;
          });
      }
      return Z({get: o, set: r}, !e, !0);
    }),
    (n.createApp = function (n, t) {
      void 0 === t && (t = void 0);
      var e = g(),
        o = void 0,
        i = {},
        u = {
          config: e.config,
          use: e.use.bind(e),
          mixin: e.mixin.bind(e),
          component: e.component.bind(e),
          provide: function (n, t) {
            return (i[n] = t), this;
          },
          directive: function (n, t) {
            return t ? (e.directive(n, t), u) : e.directive(n);
          },
          mount: function (u, f) {
            return (
              o ||
              ((o = new e(
                r(r({propsData: t}, n), {provide: r(r({}, i), n.provide)})
              )).$mount(u, f),
              o)
            );
          },
          unmount: function () {
            o && (o.$destroy(), (o = void 0));
          },
        };
      return u;
    }),
    (n.createRef = Z),
    (n.customRef = function (n) {
      var t = nn(0);
      return Z(
        n(
          function () {
            t.value;
          },
          function () {
            ++t.value;
          }
        )
      );
    }),
    (n.default = Yn),
    (n.defineAsyncComponent = function (n) {
      V(n) && (n = {loader: n});
      var t = n.loader,
        e = n.loadingComponent,
        r = n.errorComponent,
        o = n.delay,
        i = void 0 === o ? 200 : o,
        u = n.timeout;
      n.suspensible;
      var f = n.onError,
        a = null,
        c = 0,
        l = function () {
          var n;
          return (
            a ||
            (n = a =
              t()
                .catch(function (n) {
                  if (((n = n instanceof Error ? n : new Error(String(n))), f))
                    return new Promise(function (t, e) {
                      f(
                        n,
                        function () {
                          return t((c++, (a = null), l()));
                        },
                        function () {
                          return e(n);
                        },
                        c + 1
                      );
                    });
                  throw n;
                })
                .then(function (t) {
                  return n !== a && a
                    ? a
                    : (t &&
                        (t.__esModule || "Module" === t[Symbol.toStringTag]) &&
                        (t = t.default),
                      t);
                }))
          );
        };
      return function () {
        return {component: l(), delay: i, timeout: u, error: r, loading: e};
      };
    }),
    (n.defineComponent = function (n) {
      return n;
    }),
    (n.del = function (n, t) {
      if ((g().util.warn, M(n) && U(t))) n.splice(t, 1);
      else {
        var e = n.__ob__;
        n._isVue ||
          (e && e.vmCount) ||
          (C(n, t) && (delete n[t], e && e.dep.notify()));
      }
    }),
    (n.effectScope = function (n) {
      return new c(n);
    }),
    (n.getCurrentInstance = $),
    (n.getCurrentScope = l),
    (n.h = function () {
      for (var n, t = [], e = 0; e < arguments.length; e++) t[e] = arguments[e];
      var r =
        (null == this ? void 0 : this.proxy) ||
        (null === (n = $()) || void 0 === n ? void 0 : n.proxy);
      return r
        ? r.$createElement.apply(r, t)
        : (Fn || (Fn = z(g()).$createElement), Fn.apply(Fn, t));
    }),
    (n.inject = function (n, t, e) {
      var r;
      void 0 === e && (e = !1);
      var o = null === (r = $()) || void 0 === r ? void 0 : r.proxy;
      if (o) {
        if (!n) return t;
        var i = Tn(n, o);
        return i !== Bn
          ? i
          : arguments.length > 1
          ? e && V(t)
            ? t()
            : t
          : void 0;
      }
    }),
    (n.isRaw = un),
    (n.isReactive = fn),
    (n.isReadonly = function (n) {
      return J.has(n);
    }),
    (n.isRef = tn),
    (n.markRaw = function (n) {
      if ((!T(n) && !M(n)) || !Object.isExtensible(n)) return n;
      var t = vn();
      return (t.__v_skip = !0), R(n, "__ob__", t), H.set(n, !0), n;
    }),
    (n.nextTick = function () {
      for (var n, t = [], e = 0; e < arguments.length; e++) t[e] = arguments[e];
      return null === (n = g()) || void 0 === n
        ? void 0
        : n.nextTick.apply(this, t);
    }),
    (n.onActivated = xn),
    (n.onBeforeMount = hn),
    (n.onBeforeUnmount = wn),
    (n.onBeforeUpdate = gn),
    (n.onDeactivated = On),
    (n.onErrorCaptured = jn),
    (n.onMounted = bn),
    (n.onScopeDispose = function (n) {
      e && e.cleanups.push(n);
    }),
    (n.onServerPrefetch = Sn),
    (n.onUnmounted = $n),
    (n.onUpdated = mn),
    (n.provide = function (n, t) {
      var e,
        r = null === (e = W()) || void 0 === e ? void 0 : e.proxy;
      if (r) {
        if (!r._provided) {
          var o = {};
          E(r, "_provided", {
            get: function () {
              return o;
            },
            set: function (n) {
              return Object.assign(o, n);
            },
          });
        }
        r._provided[n] = t;
      }
    }),
    (n.proxyRefs = function (n) {
      var t, e, r;
      if (fn(n)) return n;
      var i = pn((((t = {})[q] = n), t));
      R(i, q, i[q], !1);
      var u = function (n) {
        E(i, n, {
          get: function () {
            return tn(i[q][n]) ? i[q][n].value : i[q][n];
          },
          set: function (t) {
            if (tn(i[q][n])) return (i[q][n].value = en(t));
            i[q][n] = en(t);
          },
        });
      };
      try {
        for (var f = o(Object.keys(n)), a = f.next(); !a.done; a = f.next()) {
          u(a.value);
        }
      } catch (n) {
        e = {error: n};
      } finally {
        try {
          a && !a.done && (r = f.return) && r.call(f);
        } finally {
          if (e) throw e.error;
        }
      }
      return i;
    }),
    (n.reactive = pn),
    (n.readonly = function (n) {
      return J.set(n, !0), n;
    }),
    (n.ref = nn),
    (n.set = L),
    (n.shallowReactive = dn),
    (n.shallowReadonly = function (n) {
      var t, e;
      if (!B(n)) return n;
      if ((!T(n) && !M(n)) || (!Object.isExtensible(n) && !tn(n))) return n;
      var r = tn(n) ? new Y({}) : fn(n) ? ln({}) : {},
        i = pn({}).__ob__,
        u = function (t) {
          var e,
            o = n[t],
            u = Object.getOwnPropertyDescriptor(n, t);
          if (u) {
            if (!1 === u.configurable && !tn(n)) return "continue";
            e = u.get;
          }
          E(r, t, {
            get: function () {
              var t = e ? e.call(n) : o;
              return i.dep.depend(), t;
            },
            set: function (n) {},
          });
        };
      try {
        for (var f = o(Object.keys(n)), a = f.next(); !a.done; a = f.next()) {
          u(a.value);
        }
      } catch (n) {
        t = {error: n};
      } finally {
        try {
          a && !a.done && (e = f.return) && e.call(f);
        } finally {
          if (t) throw t.error;
        }
      }
      return J.set(r, !0), r;
    }),
    (n.shallowRef = function (n) {
      var t;
      if (tn(n)) return n;
      var e = dn((((t = {})[q] = n), t));
      return Z({
        get: function () {
          return e[q];
        },
        set: function (n) {
          return (e[q] = n);
        },
      });
    }),
    (n.toRaw = function (n) {
      var t;
      return un(n) || !Object.isExtensible(n)
        ? n
        : (null === (t = null == n ? void 0 : n.__ob__) || void 0 === t
            ? void 0
            : t.value) || n;
    }),
    (n.toRef = on),
    (n.toRefs = rn),
    (n.triggerRef = function (n) {
      tn(n) && (X(!0), (n.value = n.value), X(!1));
    }),
    (n.unref = en),
    (n.useAttrs = function () {
      return In().attrs;
    }),
    (n.useCSSModule = zn),
    (n.useCssModule = Wn),
    (n.useSlots = function () {
      return In().slots;
    }),
    (n.version = "1.7.0"),
    (n.warn = function (n) {
      var t, e, r, o;
      (e = n),
        (r = null === (t = $()) || void 0 === t ? void 0 : t.proxy),
        (o = m()) && o.util
          ? o.util.warn(e, r)
          : console.warn("[vue-composition-api] ".concat(e));
    }),
    (n.watch = function (n, t, e) {
      var o = null;
      V(t) ? (o = t) : ((e = t), (o = null));
      var i = (function (n) {
        return r({immediate: !1, deep: !1, flush: "pre"}, n);
      })(e);
      return Dn(Rn(), n, o, i);
    }),
    (n.watchEffect = An),
    (n.watchPostEffect = function (n) {
      return An(n, {flush: "post"});
    }),
    (n.watchSyncEffect = function (n) {
      return An(n, {flush: "sync"});
    }),
    Object.defineProperty(n, "__esModule", {value: !0});
});
