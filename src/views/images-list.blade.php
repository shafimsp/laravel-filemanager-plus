<div class="container">

  @if((sizeof($file_info) > 0) || (sizeof($directories) > 0))
  <table class="table table-condensed table-striped">
    <thead>
      <th>{{ Lang::get('laravel-filemanager::lfm.title-item') }}</th>
      <th>{{ Lang::get('laravel-filemanager::lfm.title-size') }}</th>
      <th>{{ Lang::get('laravel-filemanager::lfm.title-type') }}</th>
      <th>{{ Lang::get('laravel-filemanager::lfm.title-modified') }}</th>
      <th>{{ Lang::get('laravel-filemanager::lfm.title-action') }}</th>
    </thead>
    <tbody>
      @foreach($directories as $key => $dir_name)
      <tr>
        <td>
          <i class="fa fa-folder-o"></i>
          <a class="folder-item pointer" data-id="{{ $dir_name['long'] }}">
            {{ $dir_name['short'] }}
          </a>
        </td>
        <td></td>
        <td>{{ Lang::get('laravel-filemanager::lfm.type-folder') }}</td>
        <td></td>
        <td></td>
      </tr>
      @endforeach

      @foreach($file_info as $file)
      <tr>
        <td>
          <i class="fa fa-image"></i>
          <?php $file_name = $file['name'];?>
          <a href="javascript:fileView('{{ $file_name }}')">
            {{ $file_name }}
          </a>
          &nbsp;&nbsp;
          <a href="javascript:edit('{{ $file_name }}')">
            <i class="fa fa-edit"></i>
          </a>
        </td>
        <td>
          {{ $file['size'] }}
        </td>
        <td>
          {{ $file['type'] }}
        </td>
        <td>
          {{ date("Y-m-d h:m", $file['created']) }}
        </td>
        <td>
          <a href="javascript:trash('{{ $file_name }}')">
            <i class="fa fa-trash fa-fw"></i>
          </a>
          {{--<a href="javascript:notImp()">--}}
          {{--<i class="fa fa-rotate-left fa-fw"></i>--}}
          {{--</a>--}}
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>

  @else
  <div class="row">
    <div class="col-md-12">
      <p>{{ Lang::get('laravel-filemanager::lfm.message-empty') }}</p>
    </div>
  </div>
  @endif

</div>
