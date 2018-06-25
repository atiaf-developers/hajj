@extends('layouts.backend')

@section('pageTitle', _lang('app.dashboard'))

@section('breadcrumb')
<li>
    <a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a>
</li>
@endsection
@section('css')
<link href="{{url('public/backend/plugins/select2/css')}}/select2.min.css" rel="stylesheet" type="text/css" />
@endsection
@section('js')
<script src="{{url('public/backend/plugins/select2/js')}}/select2.full.min.js" type="text/javascript"></script>
<script>
    $(".js-data-example-ajax").select2({
  ajax: {
     url: config.admin_url + '/ajax/getPilgrimsForAccommodation',
    dataType: 'json',
    delay: 250,
    data: function (params) {
      return {
        q: params.term, // search term
        page: params.page
      };
    },
    processResults: function (data, params) {
      // parse the results into the format expected by Select2
      // since we are using custom formatting functions we do not need to
      // alter the remote JSON data, except to indicate that infinite
      // scrolling can be used
      params.page = params.page || 1;

      return {
        results: data.results,
        pagination: {
          more: (params.page * 30) < data.total_count
        }
      };
    },
    cache: true
  },
  placeholder: 'Search for a repository',
  escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
  minimumInputLength: 1,
  templateResult: formatRepo,
  templateSelection: formatRepoSelection
});

function formatRepo (repo) {
  if (repo.loading) {
    return repo.text;
  }

  var markup = "<div class='select2-result-repository clearfix'>" +
    "<div class='select2-result-repository__avatar'><img src='" + repo.text + "' /></div>" +
    "<div class='select2-result-repository__meta'>" +
      "<div class='select2-result-repository__title'>" + repo.text + "</div>";

  if (repo.description) {
    markup += "<div class='select2-result-repository__description'>" + repo.text + "</div>";
  }

  markup += "<div class='select2-result-repository__statistics'>" +
    "<div class='select2-result-repository__forks'><i class='fa fa-flash'></i> " + repo.text + " Forks</div>" +
    "<div class='select2-result-repository__stargazers'><i class='fa fa-star'></i> " + repo.text + " Stars</div>" +
    "<div class='select2-result-repository__watchers'><i class='fa fa-eye'></i> " + repo.text + " Watchers</div>" +
  "</div>" +
  "</div></div>";

  return markup;
}

function formatRepoSelection (repo) {
  return repo.full_name || repo.text;
}
function matchStart(params, data) {
    console.log('term'.params.term);
    params.term = params.term || '';
    if (data.text.toUpperCase().indexOf(params.term.toUpperCase()) == 0) {
        return data;
    }
    return null;
}
$(".js-data-exampssle-ajax").select2({
    minimumInputLength: 2,

    formatNoMatches: function () {
  return "No Results Found <a href='#' class='btn btn-danger'>Use it anyway</a>";
  },
    tags: [],
    ajax: {
        url: config.admin_url + '/ajax/getPilgrimsForAccommodation',
        dataType: 'json',
        type: "GET",
        quietMillis: 50,
        data: function (term) {
            console.log(term);
            return {
                term: term
            };
        }
//        ,
//        results: function (data) {
//            console.log(data);
//            return {
//                results: $.map(data, function (item) {
//                    return {
//                        text: item.completeName,
//                        slug: item.slug,
//                        id: item.id
//                    }
//                })
//            };
//        }
        , processResults: function (data) {
            // Tranforms the top-level key of the response object from 'items' to 'results'
            console.log(data);
            return {
                results: data.results
            };
        },
            matcher: matchStart,
    }
});
$('.js-data-exasmple-ajax').select2({
    ajax: {
        url: config.admin_url + '/ajax/getPilgrimsForAccommodation',
        data: function (params) {
            var query = {
                search: params.term,
                type: 'public'
            }

            // Query parameters will be ?search=[term]&type=public
            return query;
        },
        processResults: function (data) {
            // Tranforms the top-level key of the response object from 'items' to 'results'
            console.log(data);
//            return {
//                results: data.items
//            };
        }
    }
});
</script>
@endsection

@section('content')
<div class="row" style="margin-top: 40px;">

    <div class="col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 blue" href="http://wsool.co/admin/clients">
            <div class="visual">
                <i class="fa fa-comments"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup" data-value="28">28</span>
                </div>
                <div class="desc">العملاء</div>
            </div>
        </a>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 red-flamingo" href="http://wsool.co/admin/companies">
            <div class="visual">
                <i class="fa fa-comments"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup" data-value="2">2</span>
                </div>
                <div class="desc">الشركات الخدمية</div>
            </div>
        </a>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 green-haze" href="http://wsool.co/admin/companies">
            <div class="visual">
                <i class="fa fa-comments"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup" data-value="18">18</span>
                </div>
                <div class="desc">الشركات الصناعية</div>
            </div>
        </a>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 green-haze" href="http://wsool.co/admin/companies">
            <div class="visual">
                <i class="fa fa-comments"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup" data-value="18">18</span>
                </div>
                <div class="desc">الشركات الصناعية</div>
            </div>
        </a>
    </div>

    <div class="form-group form-md-line-input col-md-12">
        <select class="js-data-example-ajax col-md-6"></select>
    </div>


</div>

@endsection