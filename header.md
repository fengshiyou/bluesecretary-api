## 接口参数规范
### Header参数(业务无关参数)
* app       应用名
* version   版本号
* xqid      imei或者idfa
* os        手机系统 ios,android
* timestamp 时间戳
* uid       用户id
* token     服务端参数
* jd        经度
* wd        维度

* 签名验证规则
1. 将头文件参数和所有的请求参数按字典序排序，并按key=value的方式拼接在一起，组成一个字符串
2. 将第一步生成的字符串与密钥1和密钥2拼接    拼接规则： 字符串 + md5(密钥1) + 密钥2
示例：app=10os=30timestamp=40token=50uid=60user=11111version=70xqid=8044222535b40d97c5f4fe1a86a0e5c8e708dbb4b4425921c381462cfc48a3f5c0

3. 将第二步生成的字符串MD5加密后发送给服务端，key为sign



其余参数在post请求体后者get请求url传递
