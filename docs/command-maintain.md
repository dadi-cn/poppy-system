# 命令行 - 维护

## 多图片/视频上传

```
{!! Form::multiThumb('images', [], $options) !!}
```

| options | 类型 | 默认值 | 备注 |
| --- | --- | --- | --- |
| pam | object | null | 当前用户对象, 用于上传文件的授权 |
| type | string | image | 允许传入的文件类型支持 (image\*图片;video\*视频;picture*音视频) |
| sequence | bool | false | 是否支持排序 |
| number | int | 3 | 本表单允许上传的最大数量 |