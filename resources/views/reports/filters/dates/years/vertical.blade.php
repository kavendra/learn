{{--Year Range--}}
<div class="row">
    <div class="col-xs-4">
        <div class="form-group">
            {{  Form::label('inYear[]', 'Select Year')  }}
            <!--Display years in progression, last and current are selected-->
            {{  Form::selectYear('inYear[]', $yearRangeStart, $yearRangeEnd, $filterInYear , ['class'=>'form-control','multiple'=>'multiple'])  }}
        </div>
    </div>
</div>

<div class="help-block">To select multiple years, hold Ctrl key on your keyboard while making selection.</div>
