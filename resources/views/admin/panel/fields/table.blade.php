{{-- array input --}}

<?php
    $max = isset($field['max']) && (int) $field['max'] > 0 ? $field['max'] : -1;
    $min = isset($field['min']) && (int) $field['min'] > 0 ? $field['min'] : -1;
    $item_name = strtolower( isset($field['entity_singular']) && !empty($field['entity_singular']) ? $field['entity_singular'] : $field['label']);

    $items = old($field['name']) ? (old($field['name'])) : (isset($field['value']) ? ($field['value']) : (isset($field['default']) ? ($field['default']) : '' ));

    // make sure not matter the attribute casting
    // the $items variable contains a properly defined JSON
    if(is_array($items)) {
        if (count($items)) {
            $items = json_encode($items);
        }
        else
        {
            $items = '[]';
        }
    } elseif (is_string($items) && !is_array(json_decode($items))) {
        $items = '[]';
    }

?>
<div ng-app="laravelTableApp" ng-controller="tableController" @include('admin.panel.inc.field_wrapper_attributes') >

    <label class="form-label fw-bolder">{!! $field['label'] !!}</label>
    @include('admin.panel.fields.inc.translatable_icon')

    <input class="array-json" type="hidden" id="{{ $field['name'] }}" name="{{ $field['name'] }}">

    <div class="array-container form-group">

        <table class="table table-bordered table-striped mb-0" ng-init="field = '#{{ $field['name'] }}'; items = {{ $items }}; max = {{$max}}; min = {{$min}}; maxErrorTitle = '{{trans('admin.table_cant_add', ['entity' => $item_name])}}'; maxErrorMessage = '{{trans('admin.table_max_reached', ['max' => $max])}}'">

            <thead>
                <tr>

                    @foreach( $field['columns'] as $prop )
                    <th style="font-weight: 600!important;">
                        {{ $prop }}
                    </th>
                    @endforeach
                    <th class="text-center" ng-if="max == -1 || max > 1"> {{-- <i class="fa-solid fa-sort"></i> --}} </th>
                    <th class="text-center" ng-if="max == -1 || max > 1"> {{-- <i class="fa-regular fa-trash-can"></i> --}} </th>
                </tr>
            </thead>

            <tbody ui-sortable="sortableOptions" ng-model="items" class="table-striped">

                <tr ng-repeat="item in items" class="array-row">

                    @foreach( $field['columns'] as $prop => $label)
                    <td>
                        <input class="form-control input-sm" type="text" ng-model="item.{{ $prop }}">
                    </td>
                    @endforeach
                    <td ng-if="max == -1 || max > 1">
                        <span class="btn btn-sm btn-secondary sort-handle"><span class="sr-only">sort item</span><i class="fa-solid fa-sort" role="presentation" aria-hidden="true"></i></span>
                    </td>
                    <td ng-if="max == -1 || max > 1">
                        <button ng-hide="min > -1 && $index < min" class="btn btn-sm btn-secondary" type="button" ng-click="removeItem(item);"><span class="sr-only">delete item</span><i class="fa-regular fa-trash-can" role="presentation" aria-hidden="true"></i></button>
                    </td>
                </tr>

            </tbody>

        </table>

        <div class="array-controls btn-group mb-10">
            <button ng-if="max == -1 || items.length < max" class="btn btn-sm btn-secondary" type="button" ng-click="addItem()"><i class="fa-solid fa-plus"></i> Add {{ $item_name }}</button>
        </div>

    </div>

    {{-- HINT --}}
    @if (isset($field['hint']))
        <div class="form-text">{!! $field['hint'] !!}</div>
    @endif
</div>

{{-- ########################################## --}}
{{-- Extra CSS and JS for this particular field --}}
{{-- If a field type is shown multiple times on a form, the CSS and JS will only be loaded once --}}
@if ($xPanel->checkIfFieldIsFirstOfItsType($field, $fields))

    {{-- FIELD CSS - will be loaded in the after_styles section --}}
    @push('crud_fields_styles')
    {{-- @push('crud_fields_styles')
        {{-- YOUR CSS HERE --}}
    @endpush

    {{-- FIELD JS - will be loaded in the after_scripts section --}}
    @push('crud_fields_scripts')
        {{-- YOUR JS HERE --}}
        <script type="text/javascript" src="{{ asset('assets/plugins/angular.js/1.5.8/angular.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('assets/plugins/jqueryui/1.12.1/jquery-ui.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('assets/plugins/angular-ui-sortable/0.14.3/sortable.min.js') }}"></script>
        <script>

            window.angularApp = window.angularApp || angular.module('laravelTableApp', ['ui.sortable'], function($interpolateProvider){
                $interpolateProvider.startSymbol('<%');
                $interpolateProvider.endSymbol('%>');
            });

            window.angularApp.controller('tableController', function($scope){

                $scope.sortableOptions = {
                    handle: '.sort-handle'
                };

                $scope.addItem = function(){

                    if( $scope.max > -1 ){
                        if( $scope.items.length < $scope.max ){
                            var item = {};
                            $scope.items.push(item);
                        } else {
                            new PNotify.alert({
                                title: $scope.maxErrorTitle,
                                text: $scope.maxErrorMessage,
                                type: 'error'
                            });
                        }
                    }
                    else {
                        var item = {};
                        $scope.items.push(item);
                    }
                }

                $scope.removeItem = function(item){
                    var index = $scope.items.indexOf(item);
                    $scope.items.splice(index, 1);
                }

                $scope.$watch('items', function(a, b){

                    if( $scope.min > -1 ){
                        while($scope.items.length < $scope.min){
                            $scope.addItem();
                        }
                    }

                    if( typeof $scope.items != 'undefined' && $scope.items.length ){

                        if( typeof $scope.field != 'undefined'){
                            if( typeof $scope.field == 'string' ){
                                $scope.field = $($scope.field);
                            }
                            $scope.field.val( angular.toJson($scope.items) );
                        }
                    }
                }, true);

                if( $scope.min > -1 ){
                    for(var i = 0; i < $scope.min; i++){
                        $scope.addItem();
                    }
                }
            });

            angular.element(document).ready(function(){
                angular.forEach(angular.element('[ng-app]'), function(ctrl){
                    var ctrlDom = angular.element(ctrl);
                    if( !ctrlDom.hasClass('ng-scope') ){
                        angular.bootstrap(ctrl, [ctrlDom.attr('ng-app')]);
                    }
                });
            })

        </script>

    @endpush
@endif
{{-- End of Extra CSS and JS --}}
{{-- ########################################## --}}
