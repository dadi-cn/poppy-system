<?php namespace Poppy\System\Models;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Poppy\Framework\Http\Pagination\PageInfo;
use Poppy\System\Classes\Traits\FilterTrait;

/**
 * 登录日志
 *
 * @property int                            $id           ID
 * @property int                            $account_id   账户ID
 * @property int             $parent_id    父账号ID
 * @property string          $account_type 账户类型
 * @property string          $type         登录日志类型, success, error, warning
 * @property string          $ip           IP
 * @property string          $area_text    地区方式
 * @property string          $area_name    地区名字
 * @property Carbon          $created_at   创建时间
 * @property Carbon          $updated_at   修改时间
 * @property-read PamAccount $pam
 * @method static Builder|PamLog filter($input = [], $filter = null)
 * @method static Builder|PamLog newModelQuery()
 * @method static Builder|PamLog newQuery()
 * @method static Builder|PamLog pageFilter(PageInfo $pageInfo)
 * @method static Builder|PamLog paginateFilter($perPage = null, $columns = [], $pageName = 'page', $page = null)
 * @method static Builder|PamLog query()
 * @method static Builder|PamLog simplePaginateFilter($perPage = null, $columns = [], $pageName = 'page', $page = null)
 * @method static Builder|PamLog whereBeginsWith($column, $value, $boolean = 'and')
 * @method static Builder|PamLog whereEndsWith($column, $value, $boolean = 'and')
 * @method static Builder|PamLog whereLike($column, $value, $boolean = 'and')
 * @mixin Eloquent
 */
class PamLog extends Eloquent
{
	use FilterTrait;

	protected $table = 'pam_log';

	protected $fillable = [
		'account_id',
		'parent_id',
		'account_type',
		'type',
		'ip',
		'area_text',   // 山东济南联通
		'area_name',   // 济南
	];

	/**
	 * 链接用户表
	 * @return BelongsTo
	 */
	public function pam()
	{
		return $this->belongsTo(PamAccount::class, 'account_id', 'id');
	}
}