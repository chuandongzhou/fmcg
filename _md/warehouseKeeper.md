#仓管App API
##1.API接口通信规定
- 接口采用**HTTP**,**POST**协议
- 请求URL **192.168.2.66/api/v1**
- 附加请求参数 **ter_type**: **web端传1**, **ios端传2**, **android端传3**
- 请求返回数据格式
-
	所有数据返回都基于以下的**json**协议
	失败时返回：
	{"id":'success',"message":"String|Array",'errors':"{account:"错误原因"}"}
	id:状态值
	message:返回具体内容
	error:返回具体字段不合法
## 2.接口详细说明
### 2.1 公共模块 wk
#### 2.1.1 登陆[post] (login)
`请求参数：`
	
	account						string					账号
	password    				string					密码

`成功返回：`

	id							int     			    id
	account						string  			    账号
	password					string      			密码
	name						string      			名称
	mobile						string      			手机
	status						int        				状态

`失败返回：`

#### 2.1.2 退出登陆[get] (logout)
`请求参数：`

`成功返回：`

`失败返回：`


### 2.2 个人中心 person-center
#### 2.2.1 修改密码[post] (password)
`请求参数：`

	old_password				string					原密码
	new_password				string					新密码

`成功返回：`
   
	
`失败返回：`

### 2.3 订单 order
#### 2.3.1 订单列表[get] (list)
`请求参数：`

	condition					int/string				订单号or店铺名称
	choice						bool					1:已选车,0:未选车

`成功返回：`

	id							int						订单ID
	user_id						int						用户ID
	status						int						状态
	pay_status					int						支付状态
	pay_type					int						支付类型
	is_cancel					int						是否取消
	shipping_address_id			int						收货地址id
	goods_inventory				bool					0:不足
	user_shop_name				string					店铺名称
	status_name					string					状态名
	shipping_address			object					收货地址

	shipping 字段介绍:
	
		consigner				string					收货人
		phone					number					收货人电话
		address					object					地址信息

	address 字段介绍:

		area_name				string					地址
		address					string					详细
	
`失败返回：`

#### 2.3.2 订单详情[get] ({order_id}/detail)
`请求参数：`

`成功返回：`

	id							int						订单ID
	user_id						int						用户ID
	status						int						状态
	pay_status					int						支付状态
	pay_type					int						支付类型
	price						decimal					价格
	is_cancel					int						是否取消
	shipping_address_id			int						收货地址id
	goods_inventory				bool					0:不足
	user_shop_name				string					店铺名称
	status_name					string					状态名
	remark						string					订单备注and陈列费备注
	display_fee					decimal					陈列费
	created_at					time					下单时间
	paid_at						time					支付时间
	shipping_address			array					收货地址
	dispatch_truck				array					发车单
	goods						array					订单商品
	mortgageGoods				array					陈列费商品
	giftGoods					array					赠品

	order_goods 字段介绍:

	id							int						id
    type						int						商品类型(同订百达)
    goods_id					int						商品ID
    price						decimal					价格
    num							int						数量
    total_price					decimal					总价
    pieces						int						单位代码
    order_id					int						所属订单ID
    inventory_state				int						库存状态?
    goods_inventory				bool					1:足,0:不足
    pieces_name					string					单位名
    goods_name					string					商品名
    image						string					图片地
	
	dispatch_truck 字段介绍:
	
		truck					array					卡车信息
		delivery_mans			array					配送人
	
	truck 字段介绍:

		name					string					卡车名
		license_plate			string					车牌

	delivery_mans 字段介绍:
		
		同 2.4.2

	shipping 字段介绍:
	
		consigner				string					收货人
		phone					number					收货人电话
		address					array					地址信息

	address 字段介绍:

		area_name				string					地址
		address					string					详细

`失败返回：`

#### 2.3.3 修改订单商品[put] ({order_id}/modify)
`请求参数：`

	num               int            修改后的数量
	pivot_id          int            商品订单关联表ID

`成功返回：`

`失败返回：`

#### 2.3.4 删除订单商品[delete] (order-goods-delete/{order_goods_id})
`请求参数：`

`成功返回：`

`失败返回：`



### 2.4 配送单 dispatch-truck
#### 2.4.1 车辆列表[get] (trucks)

`请求参数：`

	available					bool					2：空闲 1:空闲，等待发车,0全部

`成功返回：`

	id							int						车辆ID
	name						string					车辆名
	license_plate				string					车牌号
	status						int						车辆状态(0:禁用,1,空闲,2等待发车,3配送中)
	staus_name					string					状态名
	
`失败返回：`

#### 2.4.2 配送人员列表[get] (delivery-mans)

`请求参数：`


`成功返回：`

	id						int						id
    user_name				string					账号
    name					string					名字
    phone					string					电话
	shop_id					int						店铺ID
	status					bool					状态
	delivery_status			bool					配送状态,1可以选择
	
`失败返回：`

#### 2.4.3 添加订单到车辆[post] (order)

`请求参数：`

	truck_id					int						车辆ID
	order_id					int/array				订单ID
	dispatch_truck_id			int						有就传没得就算了

`成功返回：`
	
`失败返回：`

#### 2.4.4 添加配送人员到车辆[post] (delivery-mans)

`请求参数：`

	truck_id					int						车辆ID
	delivery_mans				array					配送人ID数组

`成功返回：`
	
`失败返回：`

#### 2.4.5 修改发车单订单排序[put] ({dtv_id}/change-sort)

`请求参数：`

	order_ids					array					排序订单(key为排序顺序)

`成功返回：`
	
`失败返回：`


#### 2.4.6 创建发车单[post] (voucher-create)

`请求参数：`
	
	dtv_id						int						发车单ID
	remark						string					备注

`成功返回：`
	
`失败返回：`

#### 2.4.7 发车单详情[get] (voucher-detail)

`请求参数：`

	turck_id					int						车辆ID
	dtv_id						int						发车单ID
	注: 车辆列表传车ID其他传发车单ID

`成功返回：`
	
	id							int						发车单ID
    delivery_truck_id			int						车ID
	dispatch_truck_id			int						发车单ID,未发车就没得
    status						int						发车单状态(1:等待发车,2:配送中,3:已回车)
    remark						string					备注
    dispatch_time				time					发车时间
    back_time					time					回车时间
	can_back					bool					是否可回车,1可以回车
    order_goods_statis			array					订单商品统计
    return_order_goods_statis	array					退货订单商品统计
    orders						array					订单
    truck						array					车辆
    delivery_mans				array					配送员

	order_goods_statis 字段介绍
		goods_id				int						商品ID
		name					string					商品名称
		img_url					string					图片地址
		quantity				string					统计数量
		frequency				int						出现次数
	return_order_goods_statis 同上
	
`失败返回：`

#### 2.4.8 发车历史[get] (history)

`请求参数：`
	
	number_license				string/int				车牌/发车单号
	start_at					time					开始时间
	end_at						time					结束时间

`成功返回：`

	id							int						id
	delivery_truck_id			int						车辆id
	status						int						状态
	remark						string					发车备注
	dispatch_time				time					发车时间
	back_time					time					回车时间
	order_amount				int						订单数
	is_return_order				bool					是否有退货:1有
	truck						array					车辆信息
	
	truck字段介绍
		同2.4.1
`失败返回：`

#### 2.4.9 发车单订单商品单个统计[get] ({dtv_id}/goods-statistical)

`请求参数：`
	
	goods_id					int						商品ID

`成功返回：`

	order_id					int						订单ID
	quantity					string					数量
`失败返回：`

#### 2.4.10 发车单退货单商品单个统计[get] ({dtv_id}/return-goods-statistical)

`请求参数：`
	
	goods_id					int						商品ID

`成功返回：`

	order_id					int						订单ID
	quantity					string					数量
`失败返回：`


#### 2.4.11 切换车辆[put] ({dtv_id}/change-truck)

`请求参数：`
	
	new_truck_id				int						新车ID

`成功返回：`

`失败返回：`

#### 2.4.12 删除发车单中的订单[delete] (delete-order/{order_id})

`请求参数：`

`成功返回：`

`失败返回：`

#### 2.4.13 确认回车/确认入库[post] ({dtv_id}/truck-back)

`请求参数：`

`成功返回：`

`失败返回：`




	
	
	
	
	
	