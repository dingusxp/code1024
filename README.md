code1024
========

### 介绍

code1024 是一个有意思的 编程画图 游戏，起源是这个：

http://codegolf.stackexchange.com/questions/35569/tweetable-mathematical-art 

这里稍微做了一下扩展，放松了代码量限制和更多编程语言的支持。

### 规则：

**目标：**

通过实现 模版（/tpl/*） 中的一个函数 get_color_at(x, y) ，在一个 1024x1024 的画布上画出尽可能 “有意思” 的图案。

**参数：**

int x,  int y ，取值均为 0 - 1023，对应画布上坐标点，左上角为起始点 (0,0)

**输出：**

int r, int g, int b, int/float alpha， 对应颜色的 RGBA 分量，类型和取值，具体根据各语言的 模版 见机行事。

**要求：**

只能编辑 模版 中指定区域 // {{code start}} - // {{code end}} ，不可修改 模版 已有代码。

代码总字符数不能超过 1024 个；


### tpl 提交规范：

**命名：**

[语言]-[绘图方式]，如 PHP-gd， JS-canvas

**功能：**

通过 get_color_at(x, y) 函数，获取每一个像素点的 RGBA 颜色，在一个 1024*1024 的画布上画图。

get_color_at(x, y) 方法，默认执行绘制一张全黑的图；并预留区域 // {{code start}} - // {{code end}} 

