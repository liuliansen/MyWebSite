APP_PATH = '/admin';

Ext.onReady(function(){	
	Ext.define('Ext.form.CheckCode',{  
	    extend: 'Ext.form.field.Text',   
	    alias: 'widget.checkcode',  
	    inputTyle:'codefield',  
	    codeUrl:Ext.BLANK_IMAGE_URL,  
	    isLoader:true,  
	    onRender:function(ct,position){
	    	this.callParent(arguments);
	        this.codeEl = ct.createChild({ tag: 'img', src: Ext.BLANK_IMAGE_URL});  
	        this.codeEl.addCls('x-form-code');  
	        this.codeEl.on('click', this.loadCodeImg, this); 
	        if (this.isLoader) this.loadCodeImg(); 	        
	    },  
	    alignErrorIcon: function() {
	        this.errorIcon.alignTo(this.codeEl, 'tl-tr', [2, 0]);
	    },
	    loadCodeImg: function() {	    	
	        this.codeEl.set({ src: this.codeUrl+'&rand='+Math.random()});  
	    }  
	});
	
	var formPanel = Ext.create('Ext.form.Panel',{
		border: false,
		frame: false,
		bodyStyle: 'padding:40px 60px',	
		defaults:{
			xtype:'textfield',
			labelAlign : 'right',
			labelWidth : 60,
			width:255,
			listeners: {
                specialkey: function(field, e){                   
                    if (e.getKey() == e.ENTER) {                       
                    	var btn = loginWin.down('button');
                      	btn.fireEvent('click',btn);
                    }
                }
            }
		},
		items: [
			{
		        fieldLabel : '账&nbsp;&nbsp;&nbsp;号',
				name: 'account',    
		        allowBlank: false,
		        blankText:'账号必须填写'
		    },
		    {
		    	fieldLabel : '密&nbsp;&nbsp;&nbsp;码',
		  		name: 'password',
		  		inputType : 'password',
		        allowBlank: false,
		        blankText:'密码必须填写'
		    },
		    {
		    	cls:'CheckCode',
		    	xtype:'checkcode',
		        fieldLabel : '验证码',  
		        name : 'checkcode',     
		        allowBlank : false,  
		        isLoader:true,
		        blankText : '验证码必须填写',  
		        codeUrl: APP_PATH + '/index.php?ctrl=Login&act=mkCaptcha',
		        width : 170,
		        enableKeyEvents : true,
		        listeners:{
		        	keyup:function(me,e){		        		
		        		var code = Ext.String.trim(me.getValue());
		        		me.setValue(code.toUpperCase());
		        	},
		        	specialkey: function(field, e){                   
                    if (e.getKey() == e.ENTER) {
                    	var btn = loginWin.down('button');
                      	btn.fireEvent('click',btn);
                    }
                }
		        }
	        }	
		]
	});
	
	var loginWin = Ext.create('Ext.window.Window',{
	    title   : '请登录',
	    width   : 400,
	    border  : false,
	    closable: false,
	    onEsc   : Ext.emptyFn,
	    items   : formPanel,
	    buttons : [
			{
				text:'登录',
				listeners:{
					click: function(btn){						
						var form = formPanel.getForm();
						if(!form.isValid()){
							JsHelper.ShowWarning('登录信息不完整');
							return false;
						}
						var mask = JsHelper.mask('玩命登录中...' , btn.up('window'));
						mask.show();
						var data = form.getValues();
						data.password = CryptoJS.MD5(data.password).toString();
						data.checkcode = CryptoJS.MD5(data.checkcode).toString();
						login(data,function(){
							var chkcodefield = form.findField("checkcode");
							chkcodefield.loadCodeImg();
							mask.hide();
						});
					}
				}
			},	
			{			
				text:'清除',
				handler:function(btn){
					formPanel.getForm().reset(true);					
				}
			}
		]		
	});
	loginWin.show();
});


function login(data,callback){
	Ext.Ajax.request({
		url: APP_PATH + '/index.php',
		method: 'POST',
		params: Ext.apply(data,{ctrl:'Login',act:'login'}),
		callback: function(opt,success,res){
			callback();
			if(success){
				var rs = Ext.decode(res.responseText);
				if(rs.success){
					window.location.reload();
					return;
				}
				JsHelper.ShowError(rs.msg);
			}
			else{
				JsHelper.ShowError('操作失败,可能是网络原因.');
			}
		}
	});
}