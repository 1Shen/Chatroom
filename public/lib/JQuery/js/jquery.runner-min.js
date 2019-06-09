/*!
 * jQuery-runner - v2.3.3 - 2014-08-06
 * https://github.com/jylauril/jquery-runner/
 * Copyright (c) 2014 Jyrki Laurila <https://github.com/jylauril>
 */
(function () {
    var a, b, c, d, e, f, g, h, i;
    if (c = {
            version: "2.3.3",
            name: "jQuery-runner"
        }, g = this.jQuery || this.Zepto || this.$, !g || !g.fn) throw new Error("[" + c.name + "] jQuery or jQuery-like library is required for this plugin to work");
    e = {}, d = function (a) {
        return (10 > a ? "0" : "") + a
    }, i = 1, f = function () {
        return "runner" + i++
    }, h = function (a, b) {
        return a["r" + b] || a["webkitR" + b] || a["mozR" + b] || a["msR" + b] || function (a) {
            return setTimeout(a, 30)
        }
    }(this, "equestAnimationFrame"), b = function (a, b) {
        var c, e, f, g, h, i, j, k, l, m, n;
        for (b = b || {}, k = [36e5, 6e4, 1e3, 10], i = ["", ":", ":", "."], h = "", g = "", f = b.milliseconds, e = k.length, l = 0, 0 > a && (a = Math.abs(a), h = "-"), c = m = 0, n = k.length; n > m; c = ++m) j = k[c], l = 0, a >= j && (l = Math.floor(a / j), a -= l * j), (l || c > 1 || g) && (c !== e - 1 || f) && (g += (g ? i[c] : "") + d(l));
        return h + g
    }, a = function () {
        function a(b, c, d) {
            var h;
            return this instanceof a ? (this.items = b, h = this.id = f(), this.settings = g.extend({}, this.settings, c), e[h] = this, b.each(function (a, b) {
                g(b).data("runner", h)
            }), this.value(this.settings.startAt), void((d || this.settings.autostart) && this.start())) : new a(b, c, d)
        }
        return a.prototype.running = !1, a.prototype.updating = !1, a.prototype.finished = !1, a.prototype.interval = null, a.prototype.total = 0, a.prototype.lastTime = 0, a.prototype.startTime = 0, a.prototype.lastLap = 0, a.prototype.lapTime = 0, a.prototype.settings = {
            autostart: !1,
            countdown: !1,
            stopAt: null,
            startAt: 0,
            milliseconds: !0,
            format: null
        }, a.prototype.value = function (a) {
            this.items.each(function (b) {
                return function (c, d) {
                    var e;
                    c = g(d), e = c.is("input") ? "val" : "text", c[e](b.format(a))
                }
            }(this))
        }, a.prototype.format = function (a) {
            var c;
            return c = this.settings.format, (c = g.isFunction(c) ? c : b)(a, this.settings)
        }, a.prototype.update = function () {
            var a, b, c, d, e;
            this.updating || (this.updating = !0, c = this.settings, e = g.now(), d = c.stopAt, a = c.countdown, b = e - this.lastTime, this.lastTime = e, a ? this.total -= b : this.total += b, null !== d && (a && this.total <= d || !a && this.total >= d) && (this.total = d, this.finished = !0, this.stop(), this.fire("runnerFinish")), this.value(this.total), this.updating = !1)
        }, a.prototype.fire = function (a) {
            this.items.trigger(a, this.info())
        }, a.prototype.start = function () {
            var a;
            this.running || (this.running = !0, (!this.startTime || this.finished) && this.reset(), this.lastTime = g.now(), a = function (b) {
                return function () {
                    b.running && (b.update(), h(a))
                }
            }(this), h(a), this.fire("runnerStart"))
        }, a.prototype.stop = function () {
            this.running && (this.running = !1, this.update(), this.fire("runnerStop"))
        }, a.prototype.toggle = function () {
            this.running ? this.stop() : this.start()
        }, a.prototype.lap = function () {
            var a, b;
            return b = this.lastTime, a = b - this.lapTime, this.settings.countdown && (a = -a), (this.running || a) && (this.lastLap = a, this.lapTime = b), b = this.format(this.lastLap), this.fire("runnerLap"), b
        }, a.prototype.reset = function (a) {
            var b;
            a && this.stop(), b = g.now(), "number" != typeof this.settings.startAt || this.settings.countdown || (b -= this.settings.startAt), this.startTime = this.lapTime = this.lastTime = b, this.total = this.settings.startAt, this.value(this.total), this.finished = !1, this.fire("runnerReset")
        }, a.prototype.info = function () {
            var a;
            return a = this.lastLap || 0, {
                running: this.running,
                finished: this.finished,
                time: this.total,
                formattedTime: this.format(this.total),
                startTime: this.startTime,
                lapTime: a,
                formattedLapTime: this.format(a),
                settings: this.settings
            }
        }, a
    }(), g.fn.runner = function (b, d, f) {
        var h, i;
        switch (b || (b = "init"), "object" == typeof b && (f = d, d = b, b = "init"), h = this.data("runner"), i = h ? e[h] : !1, b) {
            case "init":
                new a(this, d, f);
                break;
            case "info":
                if (i) return i.info();
                break;
            case "reset":
                i && i.reset(d);
                break;
            case "lap":
                if (i) return i.lap();
                break;
            case "start":
            case "stop":
            case "toggle":
                if (i) return i[b]();
                break;
            case "version":
                return c.version;
            default:
                g.error("[" + c.name + "] Method " + b + " does not exist")
        }
        return this
    }, g.fn.runner.format = b
}).call(this);