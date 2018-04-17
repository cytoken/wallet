'use strict';
var shopId = sessionStorage.getItem('shopId');
var OWNER_CONF = OWNER_CONF_ALL[shopId];
var userInfo = null;
//toolFun
var ToolFun = {
    isPhone: function isPhone(text) {
        return (/^((17[0-9])|(14[0-9])|(13[0-9])|(15[^4,\D])|(18[0-9])|(19[0-9]))\d{8}$/.test(text));
    },
    isPwd: function isPwd(text) {
        return /^[0-9A-Za-z]{6,16}$/.test(text);
    },
    trimAll: function trimAll(str) {
        return str.replace(/\s*/g, "");
    },
    formatMony: function formatMony(m) {
        if (!m) return;
        var money = Number(m);
        money = money.toFixed(2);
        money += '';
        var int = money.substring(0, money.indexOf(".")).replace(/\B(?=(?:\d{3})+$)/g, ','); //取到整数部分
        var dot = money.substring(money.length, money.indexOf(".")); //取到小数部分
        money = int + dot;
        return money;
    },
    getParamName: function getParamName(attr) {
        var match = RegExp('[?&]' + attr + '=([^&]*)').exec(window.location.search);
        return match && decodeURIComponent(match[1].replace(/\+/g, ' ')); //url中+号表示空格，要替换掉
    },
};
var fillTitle = function fillTitle(title) {
    $('h1.title').text(title);
};
/*index.html*/
(function () {
    $(document).on('pageInit', '#index', function (e, id, page) {
        fillTitle(OWNER_CONF.title.proName);
        var data = {
            logoIcon: OWNER_CONF.imgPath + 'pic_logo.png',
            phoneIcon: OWNER_CONF.imgPath + 'phone.png',
            passwordIcon: OWNER_CONF.imgPath + 'password.png'
        };
        var html = template('tpl-index', data);
        $('#indexDiv').html(html);
        //登录
        $('#doLogin').on('click', function () {
            var self = $(this);
            if (self.hasClass('btn_disable')) {
                return;
            }
            var phone = $('#phone').val().trim();
            if (!ToolFun.isPhone(phone)) {
                $.toast("手机号输入有误~");
                return;
            }
            var password = $('#password').val().trim();
            if (!ToolFun.isPwd(password)) {
                $.toast("密码格式输入有误~");
                return;
            }

            self.addClass('btn_disable');
            $.showIndicator();
            //请求登录接口
            $.ajax({
                url: MOD_PATH + '/User/signIn',
                data: {
                    'phone': phone,
                    'passWord': password
                },
                success: function success(res) {
                    if (res.code == '0') {
                        $.hideIndicator();
                        $.toast("登录成功~");
                        sessionStorage.setItem('userId', res.data.id);
                        self.removeClass('btn_disable');
                        setTimeout(function () {
                            $.router.load(APP_PATH + '/Home/Index/userCenter', true);
                        }, 2000);
                    } else {
                        $.hideIndicator();
                        $.toast(res.msg || "登录失败~");
                        self.removeClass('btn_disable');
                    }
                },
                error: function error() {
                    $.hideIndicator();
                    server_error();
                    self.removeClass('btn_disable');
                }
            });
        });
        $('#goSignUp').on('click', function(){
            $.router.load(APP_PATH + '/Home/Index/signUp', true);
        });
    });
})();
(function () {
    $(document).on('pageInit', '#signUp', function (e, id, page) {
        fillTitle(OWNER_CONF.title.proName);
        var data = {
            logoIcon: OWNER_CONF.imgPath + 'pic_logo.png',
            phoneIcon: OWNER_CONF.imgPath + 'phone.png',
            passwordIcon: OWNER_CONF.imgPath + 'password.png'
        };
        var html = template('tpl-signUp', data);
        
        $('#signUpDiv').html(html);
        $('#inviteCode').val(ToolFun.getParamName('myCode'));
        $('#signUpBtn').on('click', function () {
            var self = $(this);
            if (self.hasClass('btn_disable')) {
                return;
            }
            var phone = $('#phone2').val().trim();
            if (!ToolFun.isPhone(phone)) {
                $.toast("手机号输入有误~");
                return;
            }
            var password = $('#password2').val().trim();
            if (!ToolFun.isPwd(password)) {
                $.toast("密码格式输入有误~");
                return;
            }
            var inviteCode = $('#inviteCode').val().trim();

            self.addClass('btn_disable');
            $.showIndicator();
            $.ajax({
                url: MOD_PATH + '/User/signUp',
                data: {
                    'phone': phone,
                    'passWord': password,
                    'inviteCode': inviteCode,
                },
                success: function success(res) {
                    if (res.code == '0') {
                        $.hideIndicator();
                        $.toast("注册成功~");
                        self.removeClass('btn_disable');
                        setTimeout(function () {
                            $.router.load(APP_PATH + '/Home/Index/sign', true);
                        }, 2000);
                    } else {
                        $.hideIndicator();
                        $.toast(res.msg || "登录失败~");
                        self.removeClass('btn_disable');
                    }
                },
                error: function error() {
                    $.hideIndicator();
                    server_error();
                    self.removeClass('btn_disable');
                }
            });
        });
        $('#goSignIn').on('click', function () {
            $.router.load(APP_PATH + '/Home/Index/index', true);
        });
    });
})();
/*userCenter.html*/
(function () {
    $(document).on('pageInit', '#userCenter', function (e, id, page) {
        fillTitle(OWNER_CONF.title.proName);
        $.showIndicator();
        $.ajax({
            url: MOD_PATH + '/Wallet/getWalletInfo',
            data: {
                'userId': sessionStorage.getItem('userId'),
            },
            success: function (res) {
                if (res.code == '0') {
                    $.hideIndicator();
                    var html = template('tpl-userCenter', res.data);
                    $('#userCenterDiv').html(html);
                } else {
                    $.hideIndicator();
                    $.toast(res.msg || "服务器繁忙~");
                }
            },
            error: function () {
                $.hideIndicator();
                server_error();
            }
        });
    });
})();
(function () {
    $(document).on('pageInit', '#qrCode', function (e, id, page) {
        $.showIndicator();
        $.ajax({
            url: MOD_PATH + '/User/getUserInfo',
            data: {
                'userId': sessionStorage.getItem('userId'),
            },
            success: function (res) {
                if (res.code == '0') {
                    $.hideIndicator();
                    $('#qrcodeurl').attr('src', res.data[0].qrCodeUrL);
                } else {
                    $.hideIndicator();
                    $.toast(res.msg || "服务器繁忙~");
                }
            },
            error: function () {
                $.hideIndicator();
                server_error();
            }
        });
    });
})();
(function () {
    $(document).on('pageInit', '#qrCode', function (e, id, page) {
        $('#saveAddress').off('click').on('click', function () {
            var address = $('#addressInput').val();
            if(!address){
                $.toast("请输入地址~");
                return;
            }
            $.showIndicator();
            $.ajax({
                url: MOD_PATH + '/User/updateWalletAddress',
                data: {
                    'userId': sessionStorage.getItem('userId'),
                    'walletAddress': address,
                },
                success: function (res) {
                    if (res.code == '0') {
                        $.hideIndicator();
                        $.toast("保存成功~");
                    } else {
                        $.hideIndicator();
                        $.toast(res.msg || "服务器繁忙~");
                    }
                },
                error: function () {
                    $.hideIndicator();
                    server_error();
                }
            });
        });
        $.showIndicator();
        $.ajax({
            url: MOD_PATH + '/User/getUserInfo',
            data: {
                'userId': sessionStorage.getItem('userId'),
            },
            success: function (res) {
                if (res.code == '0') {
                    $.hideIndicator();
                    $('#addressInput').val(res.data[0].walletAddress);
                } else {
                    $.hideIndicator();
                    $.toast(res.msg || "服务器繁忙~");
                }
            },
            error: function () {
                $.hideIndicator();
                server_error();
            }
        });
    });
})();
// 退出登录
(function () {
    $(document).on('click', '#logout', function () {
        $.confirm('确认退出吗？', function () {
            window.location.href = 'index';
        });
    });
})();
(function () {
    $(document).on('pageInit', '#incomeList', function (e, id, page) {
        $('.infinite-scroll-preloader').hide();
        var loading = false, pageSize = 10, currPage = 1, maxItems;
        $('#incomeListDiv').html(' ');
        $(document).on("infinite", ".infinite-scroll-bottom", function () {
            if (loading) return;
            loading = true;
            setTimeout(function () {
                if (maxItems <= (currPage - 1) * pageSize) {
                    // 加载完毕，则注销无限加载事件，以防不必要的加载
                    $.detachInfiniteScroll($('.infinite-scroll'));
                    // 删除加载提示符
                    $('.infinite-scroll-preloader').remove();
                    return;
                }
                getData();
                $.refreshScroller();
            }, 500);
        });

        /**
         * 请求数据
         */
        function getData() {
            $.showIndicator();
            $.ajax({
                url: MOD_PATH + '/Wallet/getRechargeLog',
                data: {
                    userId: sessionStorage.getItem('userId'),
                    start: currPage,
                    pagesize: pageSize
                },
                success: function success(res) {
                    $.hideIndicator();
                    if (res.code == '0' && res.data.length) {
                        var data = res.data;
                        maxItems = res.total;
                        $('#incomeListDiv').append(template('tpl-incomeList', { "data": data }));
                        loading = false;
                        currPage++;
                        if (maxItems <= pageSize) {
                            // 加载完毕，则注销无限加载事件，以防不必要的加载
                            $.detachInfiniteScroll($('.infinite-scroll'));
                            // 删除加载提示符
                            $('.infinite-scroll-preloader').remove();
                        }
                    } else {
                        $.toast("没有数据~");
                    }
                },
                error: function error() {
                    $.hideIndicator();
                    server_error();
                },
                complete: function () {
                    $.hideIndicator();
                    $('.infinite-scroll-preloader').hide();
                }
            });
        }
        getData();
    });
})();
(function () {
    $(document).on('pageInit', '#freeList', function (e, id, page) {
        $('.infinite-scroll-preloader').hide();
        var loading = false, pageSize = 10, currPage = 1, maxItems;
        $('#freeListDiv').html(' ');
        $(document).on("infinite", ".infinite-scroll-bottom", function () {
            if (loading) return;
            loading = true;
            setTimeout(function () {
                if (maxItems <= (currPage - 1) * pageSize) {
                    // 加载完毕，则注销无限加载事件，以防不必要的加载
                    $.detachInfiniteScroll($('.infinite-scroll'));
                    // 删除加载提示符
                    $('.infinite-scroll-preloader').remove();
                    return;
                }
                getData();
                $.refreshScroller();
            }, 500);
        });

        /**
         * 请求数据
         */
        function getData() {
            $.showIndicator();
            $.ajax({
                url: MOD_PATH + '/Bonus/getBonusLog',
                data: {
                    userId: sessionStorage.getItem('userId'),
                    start: currPage,
                    pagesize: pageSize
                },
                success: function success(res) {
                    $.hideIndicator();
                    if (res.code == '0' && res.data.length) {
                        var data = res.data;
                        maxItems = res.total;
                        $('#freeListDiv').append(template('tpl-freeList', { "data": data }));
                        loading = false;
                        currPage++;
                        if (maxItems <= pageSize) {
                            // 加载完毕，则注销无限加载事件，以防不必要的加载
                            $.detachInfiniteScroll($('.infinite-scroll'));
                            // 删除加载提示符
                            $('.infinite-scroll-preloader').remove();
                        }
                    } else {
                        $.toast("没有数据~");
                    }
                },
                error: function error() {
                    $.hideIndicator();
                    server_error();
                },
                complete: function () {
                    $.hideIndicator();
                    $('.infinite-scroll-preloader').hide();
                }
            });
        }
        getData();
    });
})();
(function () {
    $(document).on('pageInit', '#memberList', function (e, id, page) {
        $('.infinite-scroll-preloader').hide();
        var loading = false, pageSize = 10, currPage = 1, maxItems;
        $('#memberListDiv').html(' ');
        $(document).on("infinite", ".infinite-scroll-bottom", function () {
            if (loading) return;
            loading = true;
            setTimeout(function () {
                if (maxItems <= (currPage - 1) * pageSize) {
                    // 加载完毕，则注销无限加载事件，以防不必要的加载
                    $.detachInfiniteScroll($('.infinite-scroll'));
                    // 删除加载提示符
                    $('.infinite-scroll-preloader').remove();
                    return;
                }
                getData();
                $.refreshScroller();
            }, 500);
        });

        /**
         * 请求数据
         */
        function getData() {
            $.showIndicator();
            $.ajax({
                url: MOD_PATH + '/User/memberList',
                data: {
                    userId: sessionStorage.getItem('userId'),
                    start: currPage,
                    pagesize: pageSize
                },
                success: function success(res) {
                    $.hideIndicator();
                    if (res.code == '0' && res.data.length) {
                        var data = res.data;
                        maxItems = res.total;
                        $('#memberListDiv').append(template('tpl-memberList', { "data": data }));
                        loading = false;
                        currPage++;
                        if (maxItems <= pageSize) {
                            // 加载完毕，则注销无限加载事件，以防不必要的加载
                            $.detachInfiniteScroll($('.infinite-scroll'));
                            // 删除加载提示符
                            $('.infinite-scroll-preloader').remove();
                        }
                    } else {
                        $.toast("没有数据~");
                    }
                },
                error: function error() {
                    $.hideIndicator();
                    server_error();
                },
                complete: function () {
                    $.hideIndicator();
                    $('.infinite-scroll-preloader').hide();
                }
            });
        }
        getData();
    });
})();
$.init();