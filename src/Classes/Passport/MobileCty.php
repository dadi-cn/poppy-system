<?php namespace Poppy\System\Classes\Passport;

use Illuminate\Support\Str;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;
use Poppy\Framework\Helper\UtilHelper;
use Poppy\Sms\Action\Sms;

/**
 * 表单生成
 */
class MobileCty
{

    /**
     * 国家手机区号
     *           name_el 英文名字
     *           iso     简写
     *           name_zh 中文名字
     *           cty     国家码
     *           is_open 是否开启
     * @var array[]
     */
    private static $countries = [
        ['name_el' => 'Afghanistan', 'iso' => 'AF', 'first' => 'a', 'name_zh' => '阿富汗', 'cty' => 93, 'is_open' => 1],
        ['name_el' => 'Albania', 'iso' => 'AL', 'first' => 'a', 'name_zh' => '阿尔巴尼亚', 'cty' => 355, 'is_open' => 1],
        ['name_el' => 'Algeria', 'iso' => 'DZ', 'first' => 'a', 'name_zh' => '阿尔及利亚', 'cty' => 213, 'is_open' => 1],
        ['name_el' => 'American Samoa', 'iso' => 'AS', 'first' => 'm', 'name_zh' => '美属萨摩亚', 'cty' => 1684, 'is_open' => 1],
        ['name_el' => 'Andorra', 'iso' => 'AD ', 'first' => 'a', 'name_zh' => '安道尔', 'cty' => 376, 'is_open' => 1],
        ['name_el' => 'Angola', 'iso' => 'AO', 'first' => 'a', 'name_zh' => '安哥拉', 'cty' => 244, 'is_open' => 1],
        ['name_el' => 'Anguilla', 'iso' => 'AI', 'first' => 'a', 'name_zh' => '安圭拉', 'cty' => 1264, 'is_open' => 1],
        ['name_el' => 'Antigua and Barbuda', 'iso' => 'AG', 'first' => 'a', 'name_zh' => '安提瓜和巴布达', 'cty' => 1268, 'is_open' => 1],
        ['name_el' => 'Argentina', 'iso' => 'AR', 'first' => 'a', 'name_zh' => '阿根廷', 'cty' => 54, 'is_open' => 1],
        ['name_el' => 'Armenia', 'iso' => 'AM', 'first' => 'y', 'name_zh' => '亚美尼亚', 'cty' => 374, 'is_open' => 1],
        ['name_el' => 'Aruba', 'iso' => 'AW', 'first' => 'a', 'name_zh' => '阿鲁巴', 'cty' => 297, 'is_open' => 1],
        ['name_el' => 'Australia', 'iso' => 'AU', 'first' => 'a', 'name_zh' => '澳大利亚', 'cty' => 61, 'is_open' => 1],
        ['name_el' => 'Austria', 'iso' => 'AT', 'first' => 'a', 'name_zh' => '奥地利', 'cty' => 43, 'is_open' => 1],
        ['name_el' => 'Azerbaijan', 'iso' => 'AZ', 'first' => 'a', 'name_zh' => '阿塞拜疆', 'cty' => 994, 'is_open' => 1],
        ['name_el' => 'Bahamas', 'iso' => 'BS', 'first' => 'b', 'name_zh' => '巴哈马', 'cty' => 1242, 'is_open' => 1],
        ['name_el' => 'Bahrain', 'iso' => 'BH', 'first' => 'b', 'name_zh' => '巴林', 'cty' => 973, 'is_open' => 1],
        ['name_el' => 'Bangladesh', 'iso' => 'BD', 'first' => 'm', 'name_zh' => '孟加拉国', 'cty' => 880, 'is_open' => 1],
        ['name_el' => 'Barbados', 'iso' => 'BB', 'first' => 'b', 'name_zh' => '巴巴多斯', 'cty' => 1246, 'is_open' => 1],
        ['name_el' => 'Belarus', 'iso' => 'BY', 'first' => 'b', 'name_zh' => '白俄罗斯', 'cty' => 375, 'is_open' => 1],
        ['name_el' => 'Belgium', 'iso' => 'BE', 'first' => 'b', 'name_zh' => '比利时', 'cty' => 32, 'is_open' => 1],
        ['name_el' => 'Belize', 'iso' => 'BZ', 'first' => 'b', 'name_zh' => '伯利兹', 'cty' => 501, 'is_open' => 1],
        ['name_el' => 'Benin', 'iso' => 'BJ', 'first' => 'b', 'name_zh' => '贝宁', 'cty' => 229, 'is_open' => 1],
        ['name_el' => 'Bermuda', 'iso' => 'BM', 'first' => 'b', 'name_zh' => '百慕大群岛', 'cty' => 1441, 'is_open' => 1],
        ['name_el' => 'Bhutan', 'iso' => 'BT', 'first' => 'b', 'name_zh' => '不丹', 'cty' => 975, 'is_open' => 1],
        ['name_el' => 'Bolivia', 'iso' => 'BO', 'first' => 'b', 'name_zh' => '玻利维亚', 'cty' => 591, 'is_open' => 1],
        ['name_el' => 'Bosnia and Herzegovina', 'iso' => 'BA', 'first' => 'b', 'name_zh' => '波斯尼亚和黑塞哥维那', 'cty' => 387, 'is_open' => 1],
        ['name_el' => 'Botswana', 'iso' => 'BW', 'first' => 'b', 'name_zh' => '博茨瓦纳', 'cty' => 267, 'is_open' => 1],
        ['name_el' => 'Brazil', 'iso' => 'BR', 'first' => 'b', 'name_zh' => '巴西', 'cty' => 55, 'is_open' => 1],
        ['name_el' => 'Brunei', 'iso' => 'BN', 'first' => 'w', 'name_zh' => '文莱', 'cty' => 673, 'is_open' => 1],
        ['name_el' => 'Bulgaria', 'iso' => 'BG', 'first' => 'b', 'name_zh' => '保加利亚', 'cty' => 359, 'is_open' => 1],
        ['name_el' => 'Burkina Faso', 'iso' => 'BF', 'first' => 'b', 'name_zh' => '布基纳法索', 'cty' => 226, 'is_open' => 1],
        ['name_el' => 'Burundi', 'iso' => 'BI', 'first' => 'b', 'name_zh' => '布隆迪', 'cty' => 257, 'is_open' => 1],
        ['name_el' => 'Cambodia', 'iso' => 'KH', 'first' => 'j', 'name_zh' => '柬埔寨', 'cty' => 855, 'is_open' => 1],
        ['name_el' => 'Cameroon', 'iso' => 'CM', 'first' => 'k', 'name_zh' => '喀麦隆', 'cty' => 237, 'is_open' => 1],
        ['name_el' => 'Canada', 'iso' => 'CA', 'first' => 'j', 'name_zh' => '加拿大', 'cty' => 1, 'is_open' => 1],
        ['name_el' => 'Cape Verde', 'iso' => 'CV', 'first' => 'k', 'name_zh' => '开普', 'cty' => 238, 'is_open' => 1],
        ['name_el' => 'Cayman Islands', 'iso' => 'KY', 'first' => 'k', 'name_zh' => '开曼群岛', 'cty' => 1345, 'is_open' => 1],
        ['name_el' => 'Central African Republic', 'iso' => 'CF', 'first' => 'z', 'name_zh' => '中非共和国', 'cty' => 236, 'is_open' => 1],
        ['name_el' => 'Chad', 'iso' => 'TD', 'first' => 'z', 'name_zh' => '乍得', 'cty' => 235, 'is_open' => 1],
        ['name_el' => 'Chile', 'iso' => 'CL', 'first' => 'z', 'name_zh' => '智利', 'cty' => 56, 'is_open' => 1],
        ['name_el' => 'China', 'iso' => 'CN', 'first' => 'z', 'name_zh' => '中国', 'cty' => 86, 'is_open' => 1],
        ['name_el' => 'Colombia', 'iso' => 'CO', 'first' => 'g', 'name_zh' => '哥伦比亚', 'cty' => 57, 'is_open' => 1],
        ['name_el' => 'Comoros', 'iso' => 'KM', 'first' => 'k', 'name_zh' => '科摩罗', 'cty' => 269, 'is_open' => 1],
        ['name_el' => 'Cook Islands', 'iso' => 'CK', 'first' => 'k', 'name_zh' => '库克群岛', 'cty' => 682, 'is_open' => 1],
        ['name_el' => 'Costa Rica', 'iso' => 'CR', 'first' => 'g', 'name_zh' => '哥斯达黎加', 'cty' => 506, 'is_open' => 1],
        ['name_el' => 'Croatia', 'iso' => 'HR', 'first' => 'k', 'name_zh' => '克罗地亚', 'cty' => 385, 'is_open' => 1],
        ['name_el' => 'Cuba', 'iso' => 'CU', 'first' => 'g', 'name_zh' => '古巴', 'cty' => 53, 'is_open' => 1],
        ['name_el' => 'Curacao', 'iso' => 'CW', 'first' => 'k', 'name_zh' => '库拉索', 'cty' => 599, 'is_open' => 1],
        ['name_el' => 'Cyprus', 'iso' => 'CY', 'first' => 's', 'name_zh' => '塞浦路斯', 'cty' => 357, 'is_open' => 1],
        ['name_el' => 'Czech', 'iso' => 'CZ', 'first' => 'j', 'name_zh' => '捷克', 'cty' => 420, 'is_open' => 1],
        ['name_el' => 'Democratic Republic of the Congo', 'iso' => 'CD', 'first' => 'g', 'name_zh' => '刚果民主共和国', 'cty' => 243, 'is_open' => 1],
        ['name_el' => 'Denmark', 'iso' => 'DK', 'first' => 'd', 'name_zh' => '丹麦', 'cty' => 45, 'is_open' => 1],
        ['name_el' => 'Djibouti', 'iso' => 'DJ', 'first' => 'j', 'name_zh' => '吉布提', 'cty' => 253, 'is_open' => 1],
        ['name_el' => 'Dominica', 'iso' => 'DM', 'first' => 'd', 'name_zh' => '多米尼加', 'cty' => 1767, 'is_open' => 1],
        ['name_el' => 'Dominican Republic', 'iso' => 'DO', 'first' => 'd', 'name_zh' => '多米尼加共和国', 'cty' => 1809, 'is_open' => 1],
        ['name_el' => 'Ecuador', 'iso' => 'EC', 'first' => 'e', 'name_zh' => '厄瓜多尔', 'cty' => 593, 'is_open' => 1],
        ['name_el' => 'Egypt', 'iso' => 'EG', 'first' => 'e', 'name_zh' => '埃及', 'cty' => 20, 'is_open' => 1],
        ['name_el' => 'El Salvador', 'iso' => 'SV', 'first' => 's', 'name_zh' => '萨尔瓦多', 'cty' => 503, 'is_open' => 1],
        ['name_el' => 'Equatorial Guinea', 'iso' => 'GQ', 'first' => 'c', 'name_zh' => '赤道几内亚', 'cty' => 240, 'is_open' => 1],
        ['name_el' => 'Eritrea', 'iso' => 'ER', 'first' => 'e', 'name_zh' => '厄立特里亚', 'cty' => 291, 'is_open' => 1],
        ['name_el' => 'Estonia', 'iso' => 'EE', 'first' => 'a', 'name_zh' => '爱沙尼亚', 'cty' => 372, 'is_open' => 1],
        ['name_el' => 'Ethiopia', 'iso' => 'ET', 'first' => 'a', 'name_zh' => '埃塞俄比亚', 'cty' => 251, 'is_open' => 1],
        ['name_el' => 'Faroe Islands', 'iso' => 'FO', 'first' => 'f', 'name_zh' => '法罗群岛', 'cty' => 298, 'is_open' => 1],
        ['name_el' => 'Fiji', 'iso' => 'FJ', 'first' => 'f', 'name_zh' => '斐济', 'cty' => 679, 'is_open' => 1],
        ['name_el' => 'Finland', 'iso' => 'FI', 'first' => 'f', 'name_zh' => '芬兰', 'cty' => 358, 'is_open' => 1],
        ['name_el' => 'France', 'iso' => 'FR', 'first' => 'f', 'name_zh' => '法国', 'cty' => 33, 'is_open' => 1],
        ['name_el' => 'French Guiana', 'iso' => 'GF', 'first' => 'f', 'name_zh' => '法属圭亚那', 'cty' => 594, 'is_open' => 1],
        ['name_el' => 'French Polynesia', 'iso' => 'PF', 'first' => 'f', 'name_zh' => '法属波利尼西亚', 'cty' => 689, 'is_open' => 1],
        ['name_el' => 'Gabon', 'iso' => 'GA', 'first' => 'j', 'name_zh' => '加蓬', 'cty' => 241, 'is_open' => 1],
        ['name_el' => 'Gambia', 'iso' => 'GM', 'first' => 'g', 'name_zh' => '冈比亚', 'cty' => 220, 'is_open' => 1],
        ['name_el' => 'Georgia', 'iso' => 'GE', 'first' => 'g', 'name_zh' => '格鲁吉亚', 'cty' => 995, 'is_open' => 1],
        ['name_el' => 'Germany', 'iso' => 'DE', 'first' => 'd', 'name_zh' => '德国', 'cty' => 49, 'is_open' => 1],
        ['name_el' => 'Ghana', 'iso' => 'GH', 'first' => 'j', 'name_zh' => '加纳', 'cty' => 233, 'is_open' => 1],
        ['name_el' => 'Gibraltar', 'iso' => 'GI', 'first' => 'z', 'name_zh' => '直布罗陀', 'cty' => 350, 'is_open' => 1],
        ['name_el' => 'Greece', 'iso' => 'GR', 'first' => 'x', 'name_zh' => '希腊', 'cty' => 30, 'is_open' => 1],
        ['name_el' => 'Greenland', 'iso' => 'GL', 'first' => 'g', 'name_zh' => '格陵兰岛', 'cty' => 299, 'is_open' => 1],
        ['name_el' => 'Grenada', 'iso' => 'GD', 'first' => 'g', 'name_zh' => '格林纳达', 'cty' => 1473, 'is_open' => 1],
        ['name_el' => 'Guadeloupe', 'iso' => 'GP', 'first' => 'g', 'name_zh' => '瓜德罗普岛', 'cty' => 590, 'is_open' => 1],
        ['name_el' => 'Guam', 'iso' => 'GU', 'first' => 'g', 'name_zh' => '关岛', 'cty' => 1671, 'is_open' => 1],
        ['name_el' => 'Guatemala', 'iso' => 'GT', 'first' => 'g', 'name_zh' => '瓜地马拉', 'cty' => 502, 'is_open' => 1],
        ['name_el' => 'Guinea', 'iso' => 'GN', 'first' => 'j', 'name_zh' => '几内亚', 'cty' => 224, 'is_open' => 1],
        ['name_el' => 'Guinea-Bissau', 'iso' => 'GW', 'first' => 'j', 'name_zh' => '几内亚比绍共和国', 'cty' => 245, 'is_open' => 1],
        ['name_el' => 'Guyana', 'iso' => 'GY', 'first' => 'g', 'name_zh' => '圭亚那', 'cty' => 592, 'is_open' => 1],
        ['name_el' => 'Haiti', 'iso' => 'HT', 'first' => 'h', 'name_zh' => '海地', 'cty' => 509, 'is_open' => 1],
        ['name_el' => 'Honduras', 'iso' => 'HN', 'first' => 'h', 'name_zh' => '洪都拉斯', 'cty' => 504, 'is_open' => 1],
        ['name_el' => 'Hong Kong', 'iso' => 'HK', 'first' => 'z', 'name_zh' => '中国香港', 'cty' => 852, 'is_open' => 1],
        ['name_el' => 'Hungary', 'iso' => 'HU', 'first' => 'x', 'name_zh' => '匈牙利', 'cty' => 36, 'is_open' => 1],
        ['name_el' => 'Iceland', 'iso' => 'IS', 'first' => 'b', 'name_zh' => '冰岛', 'cty' => 354, 'is_open' => 1],
        ['name_el' => 'India', 'iso' => 'IN', 'first' => 'y', 'name_zh' => '印度', 'cty' => 91, 'is_open' => 1],
        ['name_el' => 'Indonesia', 'iso' => 'ID', 'first' => 'y', 'name_zh' => '印度尼西亚', 'cty' => 62, 'is_open' => 1],
        ['name_el' => 'Iran', 'iso' => 'IR', 'first' => 'y', 'name_zh' => '伊朗', 'cty' => 98, 'is_open' => 1],
        ['name_el' => 'Iraq', 'iso' => 'IQ', 'first' => 'y', 'name_zh' => '伊拉克', 'cty' => 964, 'is_open' => 1],
        ['name_el' => 'Ireland', 'iso' => 'IE', 'first' => 'a', 'name_zh' => '爱尔兰', 'cty' => 353, 'is_open' => 1],
        ['name_el' => 'Israel', 'iso' => 'IL', 'first' => 'y', 'name_zh' => '以色列', 'cty' => 972, 'is_open' => 1],
        ['name_el' => 'Italy', 'iso' => 'IT', 'first' => 'y', 'name_zh' => '意大利', 'cty' => 39, 'is_open' => 1],
        ['name_el' => 'Ivory Coast', 'iso' => 'CI', 'first' => 'x', 'name_zh' => '象牙海岸', 'cty' => 225, 'is_open' => 1],
        ['name_el' => 'Jamaica', 'iso' => 'JM', 'first' => 'y', 'name_zh' => '牙买加', 'cty' => 1876, 'is_open' => 1],
        ['name_el' => 'Japan', 'iso' => 'JP', 'first' => 'r', 'name_zh' => '日本', 'cty' => 81, 'is_open' => 1],
        ['name_el' => 'Jordan', 'iso' => 'JO', 'first' => 'y', 'name_zh' => '约旦', 'cty' => 962, 'is_open' => 1],
        ['name_el' => 'Kazakhstan', 'iso' => 'KZ', 'first' => 'h', 'name_zh' => '哈萨克斯坦', 'cty' => 7, 'is_open' => 1],
        ['name_el' => 'Kenya', 'iso' => 'KE', 'first' => 'k', 'name_zh' => '肯尼亚', 'cty' => 254, 'is_open' => 1],
        ['name_el' => 'Kiribati', 'iso' => 'KI', 'first' => 'j', 'name_zh' => '基里巴斯', 'cty' => 686, 'is_open' => 1],
        ['name_el' => 'Kuwait', 'iso' => 'KW', 'first' => 'k', 'name_zh' => '科威特', 'cty' => 965, 'is_open' => 1],
        ['name_el' => 'Kyrgyzstan', 'iso' => 'KG', 'first' => 'j', 'name_zh' => '吉尔吉斯斯坦', 'cty' => 996, 'is_open' => 1],
        ['name_el' => 'Laos', 'iso' => 'LA', 'first' => 'l', 'name_zh' => '老挝', 'cty' => 856, 'is_open' => 1],
        ['name_el' => 'Latvia', 'iso' => 'LV', 'first' => 'l', 'name_zh' => '拉脱维亚', 'cty' => 371, 'is_open' => 1],
        ['name_el' => 'Lebanon', 'iso' => 'LB', 'first' => 'l', 'name_zh' => '黎巴嫩', 'cty' => 961, 'is_open' => 1],
        ['name_el' => 'Lesotho', 'iso' => 'LS', 'first' => 'l', 'name_zh' => '莱索托', 'cty' => 266, 'is_open' => 1],
        ['name_el' => 'Liberia', 'iso' => 'LR', 'first' => 'l', 'name_zh' => '利比里亚', 'cty' => 231, 'is_open' => 1],
        ['name_el' => 'Libya', 'iso' => 'LY', 'first' => 'l', 'name_zh' => '利比亚', 'cty' => 218, 'is_open' => 1],
        ['name_el' => 'Liechtenstein', 'iso' => 'LI', 'first' => 'l', 'name_zh' => '列支敦士登', 'cty' => 423, 'is_open' => 1],
        ['name_el' => 'Lithuania', 'iso' => 'LT', 'first' => 'l', 'name_zh' => '立陶宛', 'cty' => 370, 'is_open' => 1],
        ['name_el' => 'Luxembourg', 'iso' => 'LU', 'first' => 'l', 'name_zh' => '卢森堡', 'cty' => 352, 'is_open' => 1],
        ['name_el' => 'Macau', 'iso' => 'MO', 'first' => 'z', 'name_zh' => '中国澳门', 'cty' => 853, 'is_open' => 1],
        ['name_el' => 'Macedonia', 'iso' => 'MK', 'first' => 'm', 'name_zh' => '马其顿', 'cty' => 389, 'is_open' => 1],
        ['name_el' => 'Madagascar', 'iso' => 'MG', 'first' => 'm', 'name_zh' => '马达加斯加', 'cty' => 261, 'is_open' => 1],
        ['name_el' => 'Malawi', 'iso' => 'MW', 'first' => 'm', 'name_zh' => '马拉维', 'cty' => 265, 'is_open' => 1],
        ['name_el' => 'Malaysia', 'iso' => 'MY', 'first' => 'm', 'name_zh' => '马来西亚', 'cty' => 60, 'is_open' => 1],
        ['name_el' => 'Maldives', 'iso' => 'MV', 'first' => 'm', 'name_zh' => '马尔代夫', 'cty' => 960, 'is_open' => 1],
        ['name_el' => 'Mali', 'iso' => 'ML', 'first' => 'm', 'name_zh' => '马里', 'cty' => 223, 'is_open' => 1],
        ['name_el' => 'Malta', 'iso' => 'MT', 'first' => 'm', 'name_zh' => '马耳他', 'cty' => 356, 'is_open' => 1],
        ['name_el' => 'Martinique', 'iso' => 'MQ', 'first' => 'm', 'name_zh' => '马提尼克', 'cty' => 596, 'is_open' => 1],
        ['name_el' => 'Mauritania', 'iso' => 'MR', 'first' => 'm', 'name_zh' => '毛里塔尼亚', 'cty' => 222, 'is_open' => 1],
        ['name_el' => 'Mauritius', 'iso' => 'MU', 'first' => 'm', 'name_zh' => '毛里求斯', 'cty' => 230, 'is_open' => 1],
        ['name_el' => 'Mayotte', 'iso' => 'YT', 'first' => 'm', 'name_zh' => '马约特', 'cty' => 269, 'is_open' => 1],
        ['name_el' => 'Mexico', 'iso' => 'MX', 'first' => 'm', 'name_zh' => '墨西哥', 'cty' => 52, 'is_open' => 1],
        ['name_el' => 'Moldova', 'iso' => 'MD', 'first' => 'm', 'name_zh' => '摩尔多瓦', 'cty' => 373, 'is_open' => 1],
        ['name_el' => 'Monaco', 'iso' => 'MC', 'first' => 'm', 'name_zh' => '摩纳哥', 'cty' => 377, 'is_open' => 1],
        ['name_el' => 'Mongolia', 'iso' => 'MN', 'first' => 'm', 'name_zh' => '蒙古', 'cty' => 976, 'is_open' => 1],
        ['name_el' => 'Montenegro', 'iso' => 'ME', 'first' => 'h', 'name_zh' => '黑山', 'cty' => 382, 'is_open' => 1],
        ['name_el' => 'Montserrat', 'iso' => 'MS', 'first' => 'm', 'name_zh' => '蒙特塞拉特岛', 'cty' => 1664, 'is_open' => 1],
        ['name_el' => 'Morocco', 'iso' => 'MA', 'first' => 'm', 'name_zh' => '摩洛哥', 'cty' => 212, 'is_open' => 1],
        ['name_el' => 'Mozambique', 'iso' => 'MZ', 'first' => 'm', 'name_zh' => '莫桑比克', 'cty' => 258, 'is_open' => 1],
        ['name_el' => 'Myanmar', 'iso' => 'MM', 'first' => 'm', 'name_zh' => '缅甸', 'cty' => 95, 'is_open' => 1],
        ['name_el' => 'Namibia', 'iso' => 'NA', 'first' => 'n', 'name_zh' => '纳米比亚', 'cty' => 264, 'is_open' => 1],
        ['name_el' => 'Nepal', 'iso' => 'NP', 'first' => 'n', 'name_zh' => '尼泊尔', 'cty' => 977, 'is_open' => 1],
        ['name_el' => 'Netherlands', 'iso' => 'NL', 'first' => 'h', 'name_zh' => '荷兰', 'cty' => 31, 'is_open' => 1],
        ['name_el' => 'New Caledonia', 'iso' => 'NC', 'first' => 'x', 'name_zh' => '新喀里多尼亚', 'cty' => 687, 'is_open' => 1],
        ['name_el' => 'New Zealand', 'iso' => 'NZ', 'first' => 'x', 'name_zh' => '新西兰', 'cty' => 64, 'is_open' => 1],
        ['name_el' => 'Nicaragua', 'iso' => 'NI', 'first' => 'n', 'name_zh' => '尼加拉瓜', 'cty' => 505, 'is_open' => 1],
        ['name_el' => 'Niger', 'iso' => 'NE', 'first' => 'n', 'name_zh' => '尼日尔', 'cty' => 227, 'is_open' => 1],
        ['name_el' => 'Nigeria', 'iso' => 'NG', 'first' => 'n', 'name_zh' => '尼日利亚', 'cty' => 234, 'is_open' => 1],
        ['name_el' => 'Norway', 'iso' => 'NO', 'first' => 'n', 'name_zh' => '挪威', 'cty' => 47, 'is_open' => 1],
        ['name_el' => 'Oman', 'iso' => 'OM', 'first' => 'a', 'name_zh' => '阿曼', 'cty' => 968, 'is_open' => 1],
        ['name_el' => 'Pakistan', 'iso' => 'PK', 'first' => 'b', 'name_zh' => '巴基斯坦', 'cty' => 92, 'is_open' => 1],
        ['name_el' => 'Palau', 'iso' => 'PW', 'first' => 'p', 'name_zh' => '帕劳', 'cty' => 680, 'is_open' => 1],
        ['name_el' => 'Palestine', 'iso' => 'BL', 'first' => 'b', 'name_zh' => '巴勒斯坦', 'cty' => 970, 'is_open' => 1],
        ['name_el' => 'Panama', 'iso' => 'PA', 'first' => 'b', 'name_zh' => '巴拿马', 'cty' => 507, 'is_open' => 1],
        ['name_el' => 'Papua New Guinea', 'iso' => 'PG', 'first' => 'b', 'name_zh' => '巴布亚新几内亚', 'cty' => 675, 'is_open' => 1],
        ['name_el' => 'Paraguay', 'iso' => 'PY', 'first' => 'b', 'name_zh' => '巴拉圭', 'cty' => 595, 'is_open' => 1],
        ['name_el' => 'Peru', 'iso' => 'PE', 'first' => 'm', 'name_zh' => '秘鲁', 'cty' => 51, 'is_open' => 1],
        ['name_el' => 'Philippines', 'iso' => 'PH', 'first' => 'f', 'name_zh' => '菲律宾', 'cty' => 63, 'is_open' => 1],
        ['name_el' => 'Poland', 'iso' => 'PL', 'first' => 'b', 'name_zh' => '波兰', 'cty' => 48, 'is_open' => 1],
        ['name_el' => 'Portugal', 'iso' => 'PT', 'first' => 'p', 'name_zh' => '葡萄牙', 'cty' => 351, 'is_open' => 1],
        ['name_el' => 'Puerto Rico', 'iso' => 'PR', 'first' => 'b', 'name_zh' => '波多黎各', 'cty' => 1787, 'is_open' => 1],
        ['name_el' => 'Qatar', 'iso' => 'QA', 'first' => 'k', 'name_zh' => '卡塔尔', 'cty' => 974, 'is_open' => 1],
        ['name_el' => 'Republic Of The Congo', 'iso' => 'CG', 'first' => 'g', 'name_zh' => '刚果共和国', 'cty' => 242, 'is_open' => 1],
        ['name_el' => 'Réunion Island', 'iso' => 'RE', 'first' => 'l', 'name_zh' => '留尼汪', 'cty' => 262, 'is_open' => 1],
        ['name_el' => 'Romania', 'iso' => 'RO', 'first' => 'l', 'name_zh' => '罗马尼亚', 'cty' => 40, 'is_open' => 1],
        ['name_el' => 'Russia', 'iso' => 'RU', 'first' => 'e', 'name_zh' => '俄罗斯', 'cty' => 7, 'is_open' => 1],
        ['name_el' => 'Rwanda', 'iso' => 'RW', 'first' => 'l', 'name_zh' => '卢旺达', 'cty' => 250, 'is_open' => 1],
        ['name_el' => 'Saint Kitts and Nevis', 'first' => 's', 'iso' => 'KN', 'name_zh' => '圣基茨和尼维斯', 'cty' => 1869, 'is_open' => 1],
        ['name_el' => 'Saint Lucia', 'iso' => 'LC', 'first' => 's', 'name_zh' => '圣露西亚', 'cty' => 1758, 'is_open' => 1],
        ['name_el' => 'Saint Pierre and Miquelon', 'iso' => 'PM', 'first' => 's', 'name_zh' => '圣彼埃尔和密克隆岛', 'cty' => 508, 'is_open' => 1],
        ['name_el' => 'Saint Vincent and The Grenadines', 'iso' => 'VC', 'first' => 's', 'name_zh' => '圣文森特和格林纳丁斯', 'cty' => 1784, 'is_open' => 1],
        ['name_el' => 'Samoa', 'iso' => 'WS', 'first' => 's', 'name_zh' => '萨摩亚', 'cty' => 685, 'is_open' => 1],
        ['name_el' => 'San Marino', 'iso' => 'SM', 'first' => 's', 'name_zh' => '圣马力诺', 'cty' => 378, 'is_open' => 1],
        ['name_el' => 'Sao Tome and Principe', 'iso' => 'ST', 'first' => 's', 'name_zh' => '圣多美和普林西比', 'cty' => 239, 'is_open' => 1],
        ['name_el' => 'Saudi Arabia', 'iso' => 'SA', 'first' => 's', 'name_zh' => '沙特阿拉伯', 'cty' => 966, 'is_open' => 1],
        ['name_el' => 'Senegal', 'iso' => 'SN', 'first' => 's', 'name_zh' => '塞内加尔', 'cty' => 221, 'is_open' => 1],
        ['name_el' => 'Serbia', 'iso' => 'RS', 'first' => 's', 'name_zh' => '塞尔维亚', 'cty' => 381, 'is_open' => 1],
        ['name_el' => 'Seychelles', 'iso' => 'SC', 'first' => 's', 'name_zh' => '塞舌尔', 'cty' => 248, 'is_open' => 1],
        ['name_el' => 'Sierra Leone', 'iso' => 'SL', 'first' => 's', 'name_zh' => '塞拉利昂', 'cty' => 232, 'is_open' => 1],
        ['name_el' => 'Singapore', 'iso' => 'SG', 'first' => 'x', 'name_zh' => '新加坡', 'cty' => 65, 'is_open' => 1],
        ['name_el' => 'Saint Maarten (Dutch Part)', 'iso' => 'SX', 'first' => 's', 'name_zh' => '圣马丁岛（荷兰部分）', 'cty' => 1721, 'is_open' => 1],
        ['name_el' => 'Slovakia', 'iso' => 'SK', 'first' => 's', 'name_zh' => '斯洛伐克', 'cty' => 421, 'is_open' => 1],
        ['name_el' => 'Slovenia', 'iso' => 'SI', 'first' => 's', 'name_zh' => '斯洛文尼亚', 'cty' => 386, 'is_open' => 1],
        ['name_el' => 'Solomon Islands', 'iso' => 'SB', 'first' => 's', 'name_zh' => '所罗门群岛', 'cty' => 677, 'is_open' => 1],
        ['name_el' => 'Somalia', 'iso' => 'SO', 'first' => 's', 'name_zh' => '索马里', 'cty' => 252, 'is_open' => 1],
        ['name_el' => 'South Africa', 'iso' => 'ZA', 'first' => 'n', 'name_zh' => '南非', 'cty' => 27, 'is_open' => 1],
        ['name_el' => 'South Korea', 'iso' => 'KR', 'first' => 'h', 'name_zh' => '韩国', 'cty' => 82, 'is_open' => 1],
        ['name_el' => 'Spain', 'iso' => 'ES', 'first' => 'x', 'name_zh' => '西班牙', 'cty' => 34, 'is_open' => 1],
        ['name_el' => 'Sri Lanka', 'iso' => 'LK', 'first' => 's', 'name_zh' => '斯里兰卡', 'cty' => 94, 'is_open' => 1],
        ['name_el' => 'Sudan', 'iso' => 'SD', 'first' => 's', 'name_zh' => '苏丹', 'cty' => 249, 'is_open' => 1],
        ['name_el' => 'Suriname', 'iso' => 'SR', 'first' => 's', 'name_zh' => '苏里南', 'cty' => 597, 'is_open' => 1],
        ['name_el' => 'Swaziland', 'iso' => 'SZ', 'first' => 's', 'name_zh' => '斯威士兰', 'cty' => 268, 'is_open' => 1],
        ['name_el' => 'Sweden', 'iso' => 'SE', 'first' => 'r', 'name_zh' => '瑞典', 'cty' => 46, 'is_open' => 1],
        ['name_el' => 'Switzerland', 'iso' => 'CH', 'first' => 'r', 'name_zh' => '瑞士', 'cty' => 41, 'is_open' => 1],
        ['name_el' => 'Syria', 'iso' => 'SY', 'first' => 'x', 'name_zh' => '叙利亚', 'cty' => 963, 'is_open' => 1],
        ['name_el' => 'Taiwan', 'iso' => 'TW', 'first' => 'z', 'name_zh' => '中国台湾', 'cty' => 886, 'is_open' => 1],
        ['name_el' => 'Tajikistan', 'iso' => 'TJ', 'first' => 't', 'name_zh' => '塔吉克斯坦', 'cty' => 992, 'is_open' => 1],
        ['name_el' => 'Tanzania', 'iso' => 'TZ', 'first' => 't', 'name_zh' => '坦桑尼亚', 'cty' => 255, 'is_open' => 1],
        ['name_el' => 'Thailand', 'iso' => 'TH', 'first' => 't', 'name_zh' => '泰国', 'cty' => 66, 'is_open' => 1],
        ['name_el' => 'Timor-Leste', 'iso' => 'TL', 'first' => 'd', 'name_zh' => '东帝汶', 'cty' => 670, 'is_open' => 1],
        ['name_el' => 'Togo', 'iso' => 'TG', 'first' => 'd', 'name_zh' => '多哥', 'cty' => 228, 'is_open' => 1],
        ['name_el' => 'Tonga', 'iso' => 'TO', 'first' => 't', 'name_zh' => '汤加', 'cty' => 676, 'is_open' => 1],
        ['name_el' => 'Trinidad and Tobago', 'iso' => 'TT', 'first' => 't', 'name_zh' => '特立尼达和多巴哥', 'cty' => 1868, 'is_open' => 1],
        ['name_el' => 'Tunisia', 'iso' => 'TN', 'first' => 't', 'name_zh' => '突尼斯', 'cty' => 216, 'is_open' => 1],
        ['name_el' => 'Turkey', 'iso' => 'TR', 'first' => 't', 'name_zh' => '土耳其', 'cty' => 90, 'is_open' => 1],
        ['name_el' => 'Turkmenistan', 'iso' => 'TM', 'first' => 't', 'name_zh' => '土库曼斯坦', 'cty' => 993, 'is_open' => 1],
        ['name_el' => 'Turks and Caicos Islands', 'first' => 't', 'iso' => 'TC', 'name_zh' => '特克斯和凯科斯群岛', 'cty' => 1649, 'is_open' => 1],
        ['name_el' => 'Uganda', 'iso' => 'UG', 'first' => 'w', 'name_zh' => '乌干达', 'cty' => 256, 'is_open' => 1],
        ['name_el' => 'Ukraine', 'iso' => 'UA', 'first' => 'w', 'name_zh' => '乌克兰', 'cty' => 380, 'is_open' => 1],
        ['name_el' => 'United Arab Emirates', 'iso' => 'AE', 'first' => 'a', 'name_zh' => '阿拉伯联合酋长国', 'cty' => 971, 'is_open' => 1],
        ['name_el' => 'United Kingdom', 'iso' => 'GB', 'first' => 'y', 'name_zh' => '英国', 'cty' => 44, 'is_open' => 1],
        ['name_el' => 'United States', 'iso' => 'US', 'first' => 'm', 'name_zh' => '美国', 'cty' => 1, 'is_open' => 1],
        ['name_el' => 'Uruguay', 'iso' => 'UY', 'first' => 'w', 'name_zh' => '乌拉圭', 'cty' => 598, 'is_open' => 1],
        ['name_el' => 'Uzbekistan', 'iso' => 'UZ', 'first' => 'w', 'name_zh' => '乌兹别克斯坦', 'cty' => 998, 'is_open' => 1],
        ['name_el' => 'Vanuatu', 'iso' => 'VU', 'first' => 'w', 'name_zh' => '瓦努阿图', 'cty' => 678, 'is_open' => 1],
        ['name_el' => 'Venezuela', 'iso' => 'VE', 'first' => 'w', 'name_zh' => '委内瑞拉', 'cty' => 58, 'is_open' => 1],
        ['name_el' => 'Vietnam', 'iso' => 'VN', 'first' => 'y', 'name_zh' => '越南', 'cty' => 84, 'is_open' => 1],
        ['name_el' => 'Virgin Islands, British', 'iso' => 'VG', 'first' => 'y', 'name_zh' => '英属处女群岛', 'cty' => 1340, 'is_open' => 1],
        ['name_el' => 'Virgin Islands, US', 'iso' => 'VI', 'first' => 'y', 'name_zh' => '美属维尔京群岛', 'cty' => 1284, 'is_open' => 1],
        ['name_el' => 'Yemen', 'iso' => 'YE', 'first' => 'y', 'name_zh' => '也门', 'cty' => 967, 'is_open' => 1],
        ['name_el' => 'Zambia', 'iso' => 'ZM', 'first' => 'z', 'name_zh' => '赞比亚', 'cty' => 260, 'is_open' => 1],
        ['name_el' => 'Zimbabwe', 'iso' => 'ZW', 'first' => 'j', 'name_zh' => '津巴布韦', 'cty' => 263, 'is_open' => 1],
    ];

    /**
     * 开启的国家
     * @return \Illuminate\Support\Collection
     */
    public static function open()
    {
        return collect(self::$countries)->where('is_open', 1);
    }

    /**
     * 验证手机号是否符合规范
     * @param string $mobile 需要验证的手机号 86-152*** / 152***
     * @return bool
     */
    public static function validate($mobile)
    {
        $mobileWithCty = self::parse($mobile);
        if (!$mobileWithCty) {
            return false;
        }

        $country = (int) $mobileWithCty['country'];
        // 如果是后台默认手机号
        if ($country === 33023) {
            return false;
        }

        // 如果是国内
        if ($country === 86 && !UtilHelper::isMobile($mobileWithCty['mobile'])) {
            return false;
        }

        $iso = self::fetch('cty', $country, 'iso');
        if (!$iso) {
            return false;
        }

        $phoneUtil = PhoneNumberUtil::getInstance();
        try {
            $swissNumberProto = $phoneUtil->parse($mobile, $iso);
            return $phoneUtil->isValidNumber($swissNumberProto);
        } catch (NumberParseException $e) {
            return false;
        }
    }

    /**
     * 获取国家码的指定信息
     * @param string $field 要查询的字段
     * @param string $value 要查询的字段的值
     * @param string $key   需要查询的类型[name_el:英文名字, iso:简写, name_zh:中文名字, cty:国家码, is_open:是否开启]
     * @return array|string
     */
    public static function fetch($field, $value, $key = '')
    {
        $cty = collect(self::$countries)->where($field, $value)->first();
        if ($key) {
            return $cty[$key] ?? '';
        }
        return $cty;
    }

    /**
     * 获取所有的国家码
     * @return array
     */
    public static function codes()
    {
        return collect(self::$countries)->pluck('cty')->toArray();
    }

    /**
     * 根据手机号选择短信模板
     * @param string $mobile 手机号 86-152**
     * @return string
     */
    public static function smsCty($mobile)
    {
        $code = self::parse($mobile)['country'] ?? 86;
        return (int) $code === 86 ? Sms::ZH : Sms::CTY;
    }

    /**
     * 手机号拼接国家码
     * @param string $passport 手机或邮箱或用户名
     * @param int    $country  国家码
     * @return string
     */
    public static function passportCty($passport, $country = 86)
    {
        if (self::validate($country . '-' . $passport)) {
            return $country . '-' . $passport;
        }

        return $passport;
    }

    /**
     * @param string $mobile
     * @return string
     */
    public static function passportMobile($mobile)
    {
        if (Str::contains($mobile, '-')) {
            [$country, $mobile] = explode('-', $mobile);
            return $mobile;
        }

        return $mobile;
    }

    /**
     * 解析国别手机号
     * @param string $mobile 需要解析的手机号
     * @return array 返回解析的手机号 ['country' => 86,'mobile' => 155xxxxxxxx]
     */
    public static function parse($mobile)
    {
        if (Str::contains($mobile, '-')) {
            [$country, $mobile] = explode('-', $mobile);
        }
        elseif (UtilHelper::isMobile($mobile)) {
            $country = 86;
        }
        else {
            return [];
        }

        return compact('country', 'mobile');
    }
}
