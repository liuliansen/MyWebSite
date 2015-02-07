Ext.namespace("JsHelper");

// 加载tab页时 需要的重绘操作
JsHelper.ExtTabDoLayout = function(o) {
    var tab = Ext.getCmp("MasterPage_MainContent").getActiveTab();
    tab.add(o);
    tab.doLayout();
};

// 刷新当前Tab页
JsHelper.RefreshTab = function() {
    var tab = Ext.getCmp("MasterPage_MainContent").getActiveTab();
    tab.removeAll(true);
    tab.doLayout();
    tab.loader.load();
    //tab.getUpdater().refresh();
};

// 正常提示信息 msg:消息内容;animEl:从什么DOM飞出;fn:事件
JsHelper.ShowMsg = function (msg, animEl, fn) {
    Ext.Msg.show({
        title: '系统消息',
        msg: msg,
        buttons: {ok:'完成'},//{ok:'完成',  cancel:'Bar'},
        fn: fn,
        animEl: animEl,
        icon: Ext.MessageBox.INFO
    });
}

// 一般性的错误提示信息 msg:消息内容;animEl:从什么DOM飞出;fn:事件
JsHelper.ShowWarning = function(msg, animEl, fn) {
    Ext.Msg.show({
        title: '系统消息',
        msg: msg,
        buttons: Ext.Msg.OK,
        fn: fn,
        animEl: animEl,
        icon: Ext.MessageBox.WARNING
    });
}

// 系统出错提示信息 msg:消息内容;animEl:从什么DOM飞出;fn:事件
JsHelper.ShowError = function(msg, animEl, fn) {
    Ext.Msg.show({
        title: '系统错误',
        msg: msg,
        buttons: Ext.Msg.OK,
        fn: fn,
        animEl: animEl,
        icon: Ext.MessageBox.ERROR
    });
}

var msgCt;
JsHelper.ShowMessage = function(title, format) {
    if (!msgCt) {
        msgCt = Ext.DomHelper.insertFirst(document.body, { id: 'msg-div' }, true);
    }
    msgCt.alignTo(document.getElementById("MasterPage_MainContent"), 'tr-tr');
    var s = String.format.apply(String, Array.prototype.slice.call(arguments, 1));
    var m = Ext.DomHelper.append(msgCt, { html: createBox(title, s) }, true);
    m.slideIn('t').pause(1).ghost("t", { remove: true });
}


/*JsHelper.info = function(msg, title, delayTime) {
    new Ext.ux.window.MessageWindow({
        title: title || '提示信息',
        html: msg || '提示信息',
        roigin: { offY: -5, offX: -5 },
        autoHeight: true,
        iconCls: 'icon-info',
        help: false,
        //frame:false
        hideFx: { delay: delayTime || 1000, mode: 'standard' }
    }).show(Ext.getDoc());
}*/

// 删除确认框 fn:事件;animEl:从什么DOM飞出
JsHelper.DelConfirm = function(fn, animEl) {
    Ext.Msg.show({
        title: '系统提示',
        msg: '确定要删除所选项吗?',
        buttons: Ext.Msg.YESNO,
        fn: fn,
        animEl: animEl,
        icon: Ext.MessageBox.QUESTION
    });
}
// 操作确认框  msg: 提示消息；fn:事件;animEl:从什么DOM飞出
JsHelper.Confirm = function(msg, fn, animEl) {
    Ext.Msg.show({
        title: '系统提示',
        msg: msg,
        buttons: Ext.Msg.YESNO,
        fn: fn,
        animEl: animEl,
        icon: Ext.MessageBox.QUESTION
    });
}

JsHelper.info = function(msg, title, delayTime) {
	var msgs=msg || '提示信息';
	var msgBox = Ext.MessageBox.show({
			title: title || '提示信息',
			msg: msgs,
			modal:true,
			//buttons:Ext.Msg.OK,
			fn:function(){
				//停止定时任务
				Ext.TaskManager.stop(task);
			}
		})
		var timeLine= delayTime || 5;
		//Ext.TaskManager是一个功能类，用来定时执行程序，
		//在这里我们使用它来定时触发提示信息的更新。
		var task = {
			run:function(){
				timeLine--;
				msgBox.updateText(msgs+'<br/>'+timeLine+'秒后将自动关闭！');
				if(timeLine<=0){
					Ext.TaskManager.stop(task);
					msgBox.hide();
				}
			},
			interval:1000
		}
		Ext.TaskManager.start(task);
}


JsHelper.mask = function (msg,el){
	if(!msg) msg = '正在处理,请稍后...';
	if(!el) el = Ext.getBody();
	var mask = new Ext.LoadMask(el, {msg:msg});
	return mask;
}
