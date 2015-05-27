intelli.recipe = function()
{	
	return {
		oGrid: null,
		title: _t('manage_recipes'),
		url: intelli.config.admin_url + '/ajax/manage/recipes/',
		removeBtn: true,
		progressBar: true,
		texts: {
			confirm_one: _t('are_you_sure_to_delete_selected_recipe'),			
			confirm_many: _t('are_you_sure_to_delete_selected_recipes')
		},
		statusesStore: ['active','approval'],
		record:['title', 'title_alias', 'account', 'body', 'date_added', 'date_modified', 'status', 'edit', 'remove'],
		columns:[
			'checkcolumn',
			{
				custom: 'expander',
				tpl: '{body}'
			},
			{
				header: _t('title'), 
				dataIndex: 'title', 
				sortable: true, 
				width: 250,
				editor: new Ext.form.TextField({
					allowBlank: false
				})
			},{
				header: _t('title_alias'), 
				dataIndex: 'title_alias', 
				sortable: true,
				hidden: true,
				width: 200,
				editor: new Ext.form.TextField({
					allowBlank: false
				})
			},{
				header: _t('account'), 
				dataIndex: 'account', 
				sortable: true, 
				width: 250
			},{
				header: _t('date_added'), 
				dataIndex: 'date_added',
				width: 130,
				sortable: true,
				hidden: true
			},{
				header: _t('date_modified'), 
				dataIndex: 'date_modified',
				width: 130,
				sortable: true
			},'status',{
				custom: 'edit',
				redirect: intelli.config.admin_url+'/manage/recipes/edit/?id=',
				icon: 'edit-grid-ico.png',
				title: _t('edit')
			}
			,'remove'
		]
	};
}();

Ext.onReady(function()
{
	intelli.recipe.oGrid = new intelli.exGrid(intelli.recipe);
	
	intelli.recipe.oGrid.cfg.tbar = new Ext.Toolbar(
	{
		items:[
		_t('title') + ':',
		{
			xtype: 'textfield',
			name: 'searchTitle',
			id: 'searchTitle',
			width: 100,
			emptyText: _t('input_title')
		},
		'&nbsp;&nbsp;' + _t('account') + ':',
		{
			xtype: 'textfield',
			name: 'searchAccount',
			id: 'searchAccount',
			width: 100,
			emptyText: _t('input_account')
		},
		'&nbsp;&nbsp;' + _t('status') + ':',
		{
			xtype: 'combo',
			typeAhead: true,
			triggerAction: 'all',
			editable: false,
			lazyRender: true,
			store: intelli.recipe.oGrid.cfg.statusesStoreWithAll,
			value: 'all',
			width: 100,
			displayField: 'display',
			valueField: 'value',
			mode: 'local',
			id: 'stsFilter'
		},{
			text: _t('search'),
			iconCls: 'search-grid-ico',
			id: 'fltBtn',
			handler: function()
			{
				var title = Ext.getCmp('searchTitle').getValue();
				var account = Ext.getCmp('searchAccount').getValue();
				var status = Ext.getCmp('stsFilter').getValue();

				if('' != title || '' != account || '' != status)
				{
					intelli.recipe.oGrid.dataStore.baseParams =
					{
						action: 'get',
						status: status,
						account: account,
						title: title
					};

					intelli.recipe.oGrid.dataStore.reload();
				}
			}
		},
		'-',
		{
			text: _t('reset'),
			id: 'resetBtn',
			handler: function()
			{
				Ext.getCmp('searchTitle').reset();
				Ext.getCmp('searchAccount').reset();
				Ext.getCmp('stsFilter').setValue('all');

				intelli.recipe.oGrid.dataStore.baseParams =
				{
					action: 'get',
					title: '',
					account: '',
					status: ''
				};

				intelli.recipe.oGrid.dataStore.reload();
			}
		}]
	});
	intelli.recipe.oGrid.init();
	
	if(intelli.urlVal('status'))
	{
		Ext.getCmp('stsFilter').setValue(intelli.urlVal('status'));
	}

	var search = intelli.urlVal('quick_search');
	
	if(null != search)
	{
		Ext.getCmp('searchTitle').setValue(search);
	}
});