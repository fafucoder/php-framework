//为了避免冲突，将我们的方法用一个匿名方法包裹起来
;(function($){
	var Dialog=function(config){
        var _this=this;
        //默认参数配置
        this.config={
        	//对话框的宽高
        	 width:'auto',
        	 height:'auto',
        	 //对话框的提示信息
        	 message:null,
           //弹窗类型
           type:"waiting",
           //按钮类型
           buttons:null,
           // 弹出框延迟多久关闭
           delay:null,
           //对话框遮罩层透明度
           maskOpacity:null,
           // 动画
           effect:null,
        };

        //默认参数扩展
       if (config && $.isPlainObject(config)) {
       	   $.extend(this.config,config);//后一个用传递的参数来扩展前一个默认的参数
       }else{
            this.isConfig = true;
       }
       //创建基本的DOM
       this.body = $('body');
       //创建遮罩层
       this.mask = $('<div class="g-dialog-contianer">');
       //创建弹出框
       this.win = $('<div class="dialog-window">');
       //创建弹出框头部
       this.winHeader = $('<div class="dialog-header"></div>');
       //创建提示信息
       this.winContent = $('<div class="dialog-content">');
       //创建弹出框按钮组
       this.winFooter = $('<div class="dialog-footer">');
       
       //渲染Dom
       this.creat();

	};
  //设置一个静态层级 写在这里 不能写在上面应为没一次弹框都会新new一个对象 是没有关联的
  Dialog.zIndex = 10000;
	Dialog.prototype={
         // 动画效果函数
         animate:function(){
             var _this = this;
             this.win.css('-webkit-transform', 'scale(0,0)');
             window.setTimeout(function(){
                _this.win.css('-webkit-transform', 'scale(1,1)');
             },100)
             
         },
		     //创建弹出框
         creat:function(){
	           var _this_ = this,
	               config = this.config,
	               mask = this.mask,
	               win = this.win,
	               header = this.winHeader,
	               content = this.winContent,
	               footer = this.winFooter,
	               body = this.body;

             // 每一次创建都将层级加一
             Dialog.zIndex++;
             this.mask.css('zIndex', Dialog.zIndex);
             
	          //如果没有参数传递
	          //就显示默认等待的图标
	          if (this.isConfig) {

	          	   win.append(header.addClass('waiting'));

                 if (config.effect) {
                    this.animate();
                  };

	          	   mask.append(win);
	          	   body.append(mask);

	          }else{

              //根据配置参数创建相应的弹窗
              header.addClass(config.type);
              win.append(header);
              //如果传了文本信息
              if (config.message) {
                win.append(content.html(config.message));
              };
              //按钮组
              if(config.buttons){
                this.creatButtons(footer,config.buttons);
                win.append(footer);
              }
              //插入到页面
              mask.append(win);
              body.append(mask);
              //设置对话框的宽度
              if (config.width != 'auto') {
                win.width(config.width);
              };
              //设置对话框的高度
              if (config.height!='auto') {
                win.height(config.height);
              };
              //对话框遮罩透明度
              if (config.maskOpacity) {
                 mask.css('backgroundColor', 'rgba(0,0,0,'+config.maskOpacity+')');
              };
              //延迟多久关闭
              if (config.delay && config.delay !=0) {
                window.setTimeout(function(){
                   _this_.close();
                }, config.delay);
              };
              // 动画
	          	if (config.effect) {
                    this.animate();
              };

	          }

         },
         //buttons是参数哪里设置的按钮数组
         creatButtons:function(footer,buttons){
            var _this_ = this;
            //遍历每一个button
            $(buttons).each(function(i) {
               //获取按钮的样式或调以及文本
               //?表示是的话返回问号后面的值   否的话返回冒号后面的值*****
               var type=this.type?" class='"+this.type+"'":"";
               var btnText=this.text?this.text:"按钮"+(++i);
               var callback=this.callback?this.callback:null;
               var button = $("<button"+type+">"+btnText+"</button>");
               if (callback) {

                 button.on('click',function(){
                      var isclose = callback();

                      if ( isclose != false) {
                          _this_.close();
                      };
                    
                 });

               }else{
                 
                  button.on('click',function(){
                     
                      _this_.close();
                 })

               }
               footer.append(button);

            });

         },
         close:function(){
            this.mask.remove();
         }
	};

	window.Dialog=Dialog;
  // 绑定到jQuery上
  $.dialog = function(config){
    return new Dialog(config);
  }

})(jQuery);