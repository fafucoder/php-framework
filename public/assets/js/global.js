// 底部按钮
$(document).ready(function(){
(function(){
    var urllink=new Array();
    $(".footer-contianer div").each(function(i,items){
        urllink[i]=$(items).find("a");
    });
    for (var i = 0; i < urllink.length; i++) {
        if (window.location.pathname.indexOf(urllink[i].attr("href"))!=-1) {
            urllink[i].parent().siblings().find("i").css("color","#a8a8a8");
            urllink[i].parent().siblings().find("span").css("color","#000");
            urllink[i].find("i").css("color","red");
            urllink[i].find("span").css("color","red");
        }
    }
})();
});

// 购物车全选按钮事件
$(".cart-operate .all-choose i").click(function(){
    if($(this).is(".icon-ico2")){
        $(this).removeClass("icon-ico2").addClass("icon-iconfont0095").css("color","red");
        history.go(0);
    }else{
        $(".cart-img>span").text("0");
        $(".all-price>span").text("0.00");
        $(this).removeClass("icon-iconfont0095").addClass("icon-ico2").css("color","#1A1A1A");
    }   
});


// show-cart选择
$(".choose-itme").children().click(function(){
    changeColor($(this));
});

//发现按钮
$(".find-content-select .select-content i").click(function(){
    $(".find-content-select .select-content .select-more").toggle();
});

// 详细列表
$(".main-content-box").click(function(){
    $(this).siblings().find(".menu-contianer-submenu").hide();
    $(this).find(".menu-contianer-submenu").toggle();
});

// 输入框点击隐藏信息
$(".login-contianer .login-content input").click(function(){
    hideError();
});
$(".sign-contianer .sign-content input").click(function(){
    hideError();
});
$(".pass-contianer .pass-content input").click(function(){
    hideError();
});

$(".userinfo-contianer .userinfo-content input").click(function(){
    hideError();
});

// 登录
$(".login-submit").click(function(){
    var username=$(".login-username input").val();
    var password=$(".login-password input").val();
    // 用户名密码验证
    var checkuser=checkName(username);
    var checkpass=checkPass(password);
    if (checkuser != "yes") {
        $(".show-error-info").text(checkuser);
        showError();
        return false;
    }else if(checkpass !="yes"){
        $(".show-error-info").text(checkpass);
        showError();
        return false;
    }else{
        AJAX("/changeapp/login.php",'post','json',{'username':username,'password':password},loginCallback);
        return false;
    }
});

// 注册按钮
$(".sign-submit").click(function(){
    var username=$(".sign-username input").val();
    var password=$(".sign-password input").val();
    var repassword=$(".sign-repassword input").val();
    // 登录注册验证
    var checkuser=checkName(username);
    var checkpass=checkPass(password);
    if (checkuser != "yes") {
        $(".show-error-info").text(checkuser);
        showError();
        return false;
    }else if(checkpass !="yes"){
        $(".show-error-info").text(checkpass);
        showError();
        return false;
    }else if(repassword!=password){
        $(".show-error-info").text("两次密码不一致");
        showError();
        return false;
    }else{
        var identify=$(".sign-identify>input:text").val();
        if (identify.length="") {
            $(".show-error-info").text("请输入验证码");
            showError();
            return false;
        }else{
            // $.post('code.php',{'code':identify},LOGIN.SIGN.IDENTIFY_CALLBACK);
            AJAX("/changeapp/code.php",'post','json',{'code':identify},identifyCallback);
            return false;
        }
    }
});

// 修改密码
$(".pass-submit").click(function(){
    var o_pass=$(".pass-opassword input").val();
    var n_pass=$(".pass-password input").val();
    var n_repass=$(".pass-repassword input").val();
    //密码验证
    var checkpass=checkPass(n_pass);
    if(checkpass !="yes"){
        $(".show-error-info").text(checkpass);
        showError();
        return false;
    }else if(n_repass!=n_pass){
        $(".show-error-info").text("两次密码不一致");
        showError();
        return false;
    }else{
        AJAX('/changeapp/login.php','post','json',{'o_pass':o_pass,'n_pass':n_pass},passCallback);
        return false;
    }
});


// 个人信息
$(".userinfo-submit").click(function(){
    // 获取个人填写的信息
    var name=$(".userinfo-content .name input").val();
    var sex=$("input[type='radio']:checked").parent().text(); 
    var birthday=$(".userinfo-content .birthday input").val();
    var phone=$(".userinfo-content .phone input").val();
    var email=$(".userinfo-content .email input").val();
    var QQ=$(".userinfo-content .QQ input").val();
    if (name=="" || sex=="" || birthday=="" || phone=="" || email=="" || QQ=="") {
        $(".show-error-info").text("个人信息不能为空！");
        showError();
        return false;
    }else if(checkName(email) !="yes"){
        $(".show-error-info").text("邮箱格式不正确！");
        showError();
        return false;
    }else if(checkName(phone) !="yes"){
        $(".show-error-info").text("手机号码格式不正确！");
        showError();
        return false;
    }else{
        AJAX('/changeapp/setting.php','post','json',{'act':'update_userinfo','name':name,'sex':sex,'birthday':birthday,'phone':phone,'email':email,'QQ':QQ},userInfoCallback); 
    }
    
});

// 改变颜色
function changeColor(element){
    $(element).siblings().find("i").css("display","none");
    $(element).siblings().find(".sanjiaoxing").css("display",'none');
    $(element).find("i").css('display','inline-block');
    $(element).find(".sanjiaoxing").css('display','block');
}

// 验证用户名
function checkName(name){
    var ret="";
    var email_re = /^(\w-*\.*)+@(\w-?)+(\.\w{2,})+$/;
    var phone_re=/^1\d{10}$/;
    if (name=="") {
        ret="用户名不能为空";
    }else if(!phone_re.test(name) && !email_re.test(name)){
        ret="用户名为手机或者邮箱";
    }else{
        ret="yes";
    }
    return ret;
};

// 验证密码
function checkPass(pass){
    var ret="";
    if (pass.length=="") {
        ret="密码不能为空";
    }else if(pass.length>20 || pass.length<6){
        ret="密码长度为6-20";
    }else{
        ret="yes";
    }
    return ret;
};

// 显示错误信息
function showError(){
    $(".show-error-info").slideDown();
};

// 隐藏错误信息
function hideError(){
    $(".show-error-info").slideUp();
}

// 异步ajax
function AJAX(url,type,dataType,data,callback){
    $.ajax({
        url:url,
        type:type,
        dataType:dataType,
        data:data,
        success:callback,
        error:function(){
            alert("数据提交失败");
        }
    });
    return false;
};


// 登录callback
function loginCallback(data){
    if (data.code==200) {
        gotopersonal();
    }else{
        $(".show-error-info").text(data.msg);
        showError();
        return false;
    }
}
// 注册callback
function signCallback(data){
    if(data.code==200){
        // 注册成功
        toLogin();
        setTimeout("gotoLogin()","2000");

    }else{
        $(".show-error-info").text(data.msg);
        showError();
        refresh();
        return false;
    }

}

// 验证码callback
function identifyCallback(data){
    // var data=eval('('+data+')');
    if (data.status==200) {
        var username=$(".sign-username input").val();
        var password=$(".sign-password input").val();
        AJAX('/changeapp/login.php','post','json',{'username1':username,'password1':password},signCallback);
        return false;
    }else{
        $(".show-error-info").text(data.msg);
        showError();
        refresh();
        return false;
    }
}

// 修改密码callback
function passCallback(data){
    if(data.code==200){
        // /修改密码成功
        passSuccess();
        setTimeout("gotopersonal()","1500");
    }else{
        $(".show-error-info").text(data.msg);
        showError();
        return false;
    }
}

// 用户信息callback
function userInfoCallback(data){
    if(data.code==200){
        // /修改密码成功
        userInfoSuccess();
        setTimeout("gotoSetting()","1500");
    }else{
        $(".show-error-info").text(data.msg);
        showError();
        return false;
    }
}

// 跳转到登陆页面
function gotoLogin(){
    window.location.href=$(".go-to-login").find("a").attr("href");
}

// 跳转到个人中心
function gotopersonal(){
    window.location.href="/changeapp/personal.php";
}

// 跳转到设置中心
function gotoSetting(){
    window.location.href="/changeapp/view/setting.html";
}


// 刷新验证码
function refresh(){
    var url=$(".sign-identify img").attr("src").split('?')[0];
    url=url+'?'+Math.random();
    $(".sign-identify img").attr("src",url);
}


// 购买商品
$(".show-item .price .buy").click(function(){
var price =$(this).siblings().text();
var img=$(this).parents('.operate').siblings().find('img').attr("src");
var name=$(this).parent().siblings('.name').children().text();
var gid=$(this).parents("ul").children("input").attr("value");
if (price && img && name) {
    $.post('cart.php',{'gid':gid,'name':name,'price':price,'img':img,'act':'buy'},function(data){
        var data=eval('('+data+')');
        if (data.status==1) {
            sCart();
        }else{
            eCart();
        }
    });
    return false;
}

});

// 搜索页面
$(".search-window input").keyup(function(){
    var content=$(this).val();
    if (content.length !=0) {
        $(this).next().removeClass("icon-huatong").addClass("icon-guanbi").css("color","#bbb");
        // $.post("/changeapp/cart.php",{'content':content,'act':'search'},function(data){
        // });
    }else{
        $(this).next().removeClass("icon-guanbi").addClass("icon-huatong ");
    }

})

/*
*dialog弹出框
*/

// 商品添加成功
function sCart(){
    var d = $.dialog({
          type:'ok',
          message:'商品添加成功',
          buttons:[
               { 
                type:'red',
                 text:'继续购物',
                 callback:function(){
                    d.close();
                 },
               },
               { 
                 type:'green',
                 text:'立即结算',
                 callback:function(){
                    window.location.href="/changeapp/cart.php";
                    return false;
                 },
               },
          ],
    });
}

// 商品添加失败
function eCart(){
    var d = $.dialog({
          type:'warning',
          message:'商品添加失败',
          buttons:[
               { 
                 type:'red',
                 text:'继续购物',
                 callback:function(){
                    d.close();
                 },
               },
               { 
                 type:'red',
                 text:'取消',
                 callback:function(){
                    d.close();
                 },
               }
          ],
    });
}

//删除商品
function delCart(){
    event.preventDefault();
    var d=$.dialog({
          type:'warning',
          message:'你确定删除该商品吗?',
          buttons:[
               { 
                type:'red',
                 text:'取消',
                 callback:function(){
                    d.close();
                    return false;
                 },
               },
               { 
                 type:'green',
                 text:'确定',
                 callback:function(){
                    d.close();
                    return true;
                 },
               },
          ],
    });
}

// 注册成功
function toLogin(){
    var d=$.dialog({
        type:'ok',
        delay:2000,
        message:'注册成功',
        effect:true,
    });
}

// 修改密码成功
function passSuccess(){
    var d=$.dialog({
        type:'ok',
        delay:1000,
        message:'修改密码成功',
    });
}

// 用户信息保存成功
function userInfoSuccess(){
    var d=$.dialog({
        type:'ok',
        delay:1000,
        message:'保存成功',
    });
}


/**
 * 用户提交订单
 */

$(".pay-submit").on('click',function(){
    var name = $(".u").text();
    var phone = $(".p").text();
    var address = $(".my-add").text();
    var money = parseInt($(".m").text());
    $.post('pay.php',{
        'name':name,
        'phone':phone,
        'address':address,
        'money':money
    },function(result){
        if (result) {
            $.dialog({
                type:'ok',
                delay:1500,
                message:'购买成功',
                effect:true,
            }); 
            setTimeout(function(){
                window.location.href="/changeapp/personal.php";
            },1500);
        }
    });
});