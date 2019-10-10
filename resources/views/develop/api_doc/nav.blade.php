<div id="nav" class="api--nav">
    <ul class="layui-nav">
        <li class="layui-nav-item">
            <a class="layui-nav-item fa fa-home" href="/develop">
                <small> {!! $guard !!}</small>
            </a>
        </li>
        @if (isset($data['group']) )
            @foreach($data['group'] as $group_key => $group)
                <li class="layui-nav-item">
                    <a href="#">{!! $group_key !!} <span class="caret"></span></a>
                    <dl class="layui-nav-child">
                        @foreach($group as $link)
                            <dd>
                                <a href="{!! route_url('',$guard, ['url'=>$link->url, 'method' => $link->type]) !!}">
                                    {!! $link->title !!}</a></dd>
                        @endforeach
                    </dl>
                </li>
            @endforeach
        @endif
    </ul>
    @if (isset($self_menu))
        <ul class="layui-nav layui-layout-right">
            <li class="layui-nav-item">
                <a href="#" v-on:click="switchQuick"><i class="fa fa-search"></i></a>
            </li>
            <li class="layui-nav-item">
                <a href="#">帮助文档</a>
                <dl class="layui-nav-child">
                    @foreach($self_menu as $title => $link)
                        <dd><a target="_blank" href="{!! $link !!}">{!! $title !!}</a></dd>
                    @endforeach
                </dl>
            </li>
        </ul>
    @endif
    <div class="nav-ctr" id="quick_search">
        <div class="nav-search">
            <form class="layui-form">
                <div class="form-group search">
                    <input type="search" class="layui-input" id="search" placeholder="Search ApiDoc">
                </div>
            </form>
            <div class="results">
                @if (isset($data['group']) )
                    @foreach($data['group'] as $group_key => $group)
                        @foreach($group as $link)
                            <div class="interface">
                                <a href="{!! route_url('',$guard, ['url'=>$link->url, 'method' => $link->type]) !!}">
                                    <span>[{!! $group_key !!}] {!! $link->title !!}</span>
                                    <br>
                                    {!! $link->url !!}
                                </a>
                            </div>
                        @endforeach
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
<script>

function holmes(options) {

	if (typeof options != 'object') {
		throw new Error('The options need to be given inside an object like this:\nholmes({\n\tfind:".result",\n\tdynamic:false\n});\n see also https://haroen.me/holmes/doc/module-holmes.html');
	}

	// if options.find is missing, the searching won't work so we'll thrown an exceptions
	if (typeof options.find == 'undefined') {
		throw new Error('A find argument is needed. That should be a querySelectorAll for each of the items you want to match individually. You should have something like: \nholmes({\n\tfind:".result"\n});\nsee also https://haroen.me/holmes/doc/module-holmes.html');
	}

	start();

	// start listening
	function start() {

		// setting default values
		if (typeof options.input == 'undefined') {
			options.input = 'input[type=search]';
		}
		if (typeof options.placeholder == 'undefined') {
			options.placeholder = false;
		}
		if (typeof options.class == 'undefined') {
			options.class = {};
		}
		if (typeof options.class.visible == 'undefined') {
			options.class.visible = false;
		}
		if (typeof options.class.hidden == 'undefined') {
			options.class.hidden = 'hide';
		}
		if (typeof options.dynamic == 'undefined') {
			options.dynamic = false;
		}
		if (typeof options.contenteditable == 'undefined') {
			options.contenteditable = false;
		}

		// find the search and the elements
		var search = document.querySelector(options.input);
		var elements = document.querySelectorAll(options.find);
		var elementsLength = elements.length;

		// create a container for a placeholder
		if (options.placeholder) {
			var placeholder = document.createElement('div');
			placeholder.classList.add(options.class.hidden);
			placeholder.innerHTML = options.placeholder;
			elements[0].parentNode.appendChild(placeholder);
		}

		// if a visible class is given, give it to everything
		if (options.class.visible) {
			var i;
			for (i = 0; i < elementsLength; i++) {
				elements[i].classList.add(options.class.visible);
			}
		}

		// listen for input
		$(options.input).bind('input propertychange', function() {

			// by default the value isn't found
			var found = false;

			// search in lowercase
			var searchString;
			if (options.contenteditable) {
				searchString = search.textContent.toLowerCase();
			} else {
				searchString = search.value.toLowerCase();
			}

			// if the dynamic option is enabled, then we should query
			// for the contents of `elements` on every input
			if (options.dynamic) {
				elements = document.querySelectorAll(options.find);
				elementsLength = elements.length;
			}

			// loop over all the elements
			// in case this should become dynamic, query for the elements here
			var i;
			for (i = 0; i < elementsLength; i++) {

				// if the current element doesn't containt the search string
				// add the hidden class and remove the visbible class
				if (elements[i].textContent.toLowerCase().indexOf(searchString) === -1) {
					elements[i].classList.add(options.class.hidden);
					if (options.class.visible) {
						elements[i].classList.remove(options.class.visible);
					}
					// else
					// remove the hidden class and add the visible
				} else {
					elements[i].classList.remove(options.class.hidden);
					if (options.class.visible) {
						elements[i].classList.add(options.class.visible);
					}
					// the element is now found at least once
					found = true;
				}
			}
			// if the element wasn't found
			// and a placeholder is given,
			// stop hiding it now
			if (!found && options.placeholder) {
				placeholder.classList.remove(options.class.hidden);
				// otherwise hide it again
			} else {
				placeholder && placeholder.classList.add(options.class.hidden);
			}
		});
	}
}

$(function() {
	holmes({
		input       : '#search',
		find        : '#quick_search .interface',
		placeholder : '<h5> No Search Result!</h5>'
	});
	layui.element.init();
});

new Vue({
	el      : '#nav',
	data    : {
		show : 'none'
	},
	methods : {
		switchQuick : function() {
			var display = $('#quick_search').css('display');
			if (display === 'none') {
				$('#quick_search').css('display', 'block');
			} else {
				$('#quick_search').css('display', 'none');
			}
			$('#search').focus();
		}
	}
});
</script>