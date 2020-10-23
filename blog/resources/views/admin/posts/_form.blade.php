<div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
    {!! Form::label('title', 'Title', ['class' => 'col-md-2 control-label']) !!}

    <div class="col-md-8">
        {!! Form::text('title', null, ['class' => 'form-control', 'required', 'autofocus']) !!}

        <span class="help-block">
            <strong>{{ $errors->first('title') }}</strong>
        </span>
    </div>
</div>

<div class="form-group{{ $errors->has('type') ? ' has-error' : '' }}">
    {!! Form::label('Type', 'Type', ['class' => 'col-md-2 control-label' ]) !!}

    <div class="col-md-8">
    <select id="type" class="form-control" name="post_type" required>

        <option>Select Type</option>
        <option value="image">Image</option>
        <option value="video">Video Url</option>
    </select>
    <span class="help-block">
            <strong>{{ $errors->first('type') }}</strong>
        </span>
    </div> 
</div>



<div id="img" style="display:none;" class="form-group{{ $errors->has('image') ? ' has-error' : '' }}">
    {!! Form::label('image', 'Image', ['class' => 'col-md-2 control-label']) !!}

    <div class="col-md-8" id="imgInp" >
        
        <!-- {!! Form::file('image', ['class' => 'form-control','id'=>'image']) !!} -->
        <input type="file" name="image"  id="image"  >
    
        <span class="help-block">
            <strong>{{ $errors->first('image') }}</strong>
        </span>
        <img src="#" id="category-img-tag" width="285" height='175' alt="preview" /> 


    </div>
    
</div>
<!-- video link -->
<div  id="vdo" style="display:none;" class="form-group{{ $errors->has('video') ? ' has-error' : '' }}">
    {!! Form::label('Video Url', 'Video Url', ['class' => 'col-md-2 control-label']) !!}

    <div class="col-md-8">
        {!! Form::text('video_url', null, ['class' => 'form-control',  'autofocus']) !!}

        <span class="help-block">
            <strong>{{ $errors->first('video') }}</strong>
        </span>
    </div>
</div>










<div class="form-group{{ $errors->has('Description') ? ' has-error' : '' }}">
    {!! Form::label('Description', 'Description', ['class' => 'col-md-2 control-label']) !!}

    <div class="col-md-8">
        {!! Form::textarea('body', null, ['class' => 'form-control', 'required']) !!}

        <span class="help-block">
            <strong>{{ $errors->first('Description') }}</strong>
        </span>
    </div>
</div>

<div class="form-group{{ $errors->has('category_id') ? ' has-error' : '' }}">
    {!! Form::label('category_id', 'Category', ['class' => 'col-md-2 control-label']) !!}

    <div class="col-md-8">
        {!! Form::select('category_id', $categories, null, ['class' => 'form-control', 'required']) !!}

        <span class="help-block">
            <strong>{{ $errors->first('category_id') }}</strong>
        </span>
    </div>
</div>

@php
    if(isset($post)) {
        $tag = $post->tags->pluck('name')->all();
    } else {
        $tag = null;
    }
@endphp

<div class="form-group{{ $errors->has('tags') ? ' has-error' : '' }}">
    {!! Form::label('tags', 'Tag', ['class' => 'col-md-2 control-label']) !!}

    <div class="col-md-8" >
        {!! Form::select('tags[]', $tags, $tag, ['class' => 'form-control select2-tags', 'required', 'multiple']) !!}

        <span class="help-block">
            <strong>{{ $errors->first('tags') }}</strong>
        </span>
    </div>
</div>




 