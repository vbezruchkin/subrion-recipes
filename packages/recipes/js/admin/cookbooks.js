intelli.cookbook = function()
{	
	return {
		oGrid: null,
		title: _t('manage_cookbooks'),
		url: intelli.config.admin_url + '/ajax/manage/cookbooks/',
		removeBtn: true,
		progressBar: true,
		texts: {
			confirm_one: _t('are_you_sure_to_delete_selected_cookbook'),			
			confirm_many: _t('are_you_sure_to_delete_selected_cookbooks')
		},
		statusesStore: ['active','approval'],
		record:['title', 'title_alias', 'description', 'date_added', 'date_modified', 'status', 'edit', 'remove'],
		columns:[
			'checkcolumn',
			{
				custom: 'expander',
				tpl: '{description}'
			},{
				header: _t('title'), 
				dataIndex: 'title', 
				sortable: true, 
				width: 250,
				editor: new Ext.form.TextField(
				{
					allowBlank: false
				})
			},{
				header: _t('title_alias'), 
				dataIndex: 'title_alias', 
				sortable: true, 
				width: 200,
				editor: new Ext.form.TextField(
				{
					allowBlank: false
				})
			},{
				header: _t('date_added'), 
				dataIndex: 'date_added',
				width: 130,
				sortable: true
			},{
				header: _t('date_modified'), 
				dataIndex: 'date_modified',
				width: 130,
				sortable: true
			},
			'status',{
				custom: 'edit',
				redirect: intelli.config.admin_url+'/manage/cookbooks/edit/?id=',
				icon: 'edit-grid-ico.png',
				title: _t('edit')
			}
			,'remove'
		]
	};
}();

Ext.onReady(function(){
	intelli.cookbook.oGrid = new intelli.exGrid(intelli.cookbook);
	
	intelli.cookbook.oGrid.cfg.tbar = new Ext.Toolbar(
	{
		items:[
		_t('title') + ':',
		{
			xtype: 'textfield',
			name: 'searchTitle',
			id: 'searchTitle',
			emptyText: 'Enter title'
		},
		_t('status') + ':',
		{
			xtype: 'combo',
			typeAhead: true,
			triggerAction: 'all',
			editable: false,
			lazyRender: true,
			store: intelli.cookbook.oGrid.cfg.statusesStoreWithAll,
			value: 'all',
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
				var status = Ext.getCmp('stsFilter').getValue();

				if('' != title || '' != status)
				{
					intelli.cookbook.oGrid.dataStore.baseParams =
					{
						action: 'get',
						status: status,
						title: title
					};

					intelli.cookbook.oGrid.dataStore.reload();
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
				Ext.getCmp('stsFilter').setValue('all');

				intelli.cookbook.oGrid.dataStore.baseParams =
				{
					action: 'get',
					title: '',
					status: ''
				};

				intelli.cookbook.oGrid.dataStore.reload();
			}
		}]
	});
	intelli.cookbook.oGrid.init();
	
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