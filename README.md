code1024
========

### 介绍

code1024 是一个有意思的 编程画图 游戏，起源是这个：

http://codegolf.stackexchange.com/questions/35569/tweetable-mathematical-art 

这里稍微做了一下扩展，放松了代码量限制和更多编程语言的支持。

### 规则：

**目标：**

通过实现 模版（见 /tpl 目录） 中的一个函数 get_color_at(x, y) ，在一个 1024x1024 的画布上画出尽可能 “有意思” 的图案。

**参数：**

int x,  int y ，取值均为 0 - 1023，对应画布上坐标点，左上角为起始点 (0,0)

**返回：**

int r, int g, int b, int alpha， 对应颜色的 RGBA 分量，均为 0 - 255。

注意：各语言中 alpha 分量定义可能不同，由 模版 统一转换为： 0 - 255，值越大越透明。

**要求：**

只能编辑 模版 中 get_color_at(x, y) 函数体内 // {{code start}} - // {{code end}}  间的代码；

代码总字符数不能超过 1024 个；


### 模版规范：

**命名：**

暂时只允许单文件，放置在 /tpl/ 目录下；

名称格式 “[语言]-[绘图方式]”，后缀视语言定。 如 PHP-gd.php， JS-canvas.html


**功能：**

提供 get_color_at(x, y) 函数，预留区域 // {{code start}} - // {{code end}} ；

调用 get_color_at(x, y) 函数，获取每一个像素点的 RGBA 颜色，在一个 1024*1024 的画布上画出图案。


### 绘图代码提交和展示：

http://code1024.org/
