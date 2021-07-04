/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, exports) {

var registerBlockType = wp.blocks.registerBlockType;
var _wp$element = wp.element,
    Fragment = _wp$element.Fragment,
    RawHTML = _wp$element.RawHTML;
var _wp$editor = wp.editor,
    MediaUpload = _wp$editor.MediaUpload,
    AlignmentToolbar = _wp$editor.AlignmentToolbar,
    InspectorControls = _wp$editor.InspectorControls,
    InnerBlocks = _wp$editor.InnerBlocks,
    PanelColorSettings = _wp$editor.PanelColorSettings,
    BlockAlignmentToolbar = _wp$editor.BlockAlignmentToolbar,
    RichText = _wp$editor.RichText;
var _wp$components = wp.components,
    PanelBody = _wp$components.PanelBody,
    TextControl = _wp$components.TextControl,
    Button = _wp$components.Button,
    SelectControl = _wp$components.SelectControl,
    RangeControl = _wp$components.RangeControl,
    ToggleControl = _wp$components.ToggleControl,
    ServerSideRender = _wp$components.ServerSideRender;
var withSelect = wp.data.withSelect;


registerBlockType('mdlr/dynamic-block-subpages', {
	title: 'Child Pages List',
	icon: 'admin-page',
	category: 'common',
	keywords: [''],
	attributes: {
		pageID: {
			type: 'number',
			default: '0'
		}
	},
	edit: withSelect(function (select, props) {

		var postSelections = [];

		var query = {
			per_page: -1,
			parent: 0,
			status: 'publish'
		};
		return {
			allPages: wp.data.select('core').getEntityRecords('postType', 'page', query)
		};
	})(function (props) {
		var pageID = props.attributes.pageID,
		    setAttributes = props.setAttributes;


		var pageOptionsHtml = props.allPages && props.allPages.map(function (i, el) {
			return wp.element.createElement(
				'option',
				{ value: '' + i.id, key: el },
				i.title.rendered
			);
		});

		var setPage = function setPage(event) {

			var selected = event.target.querySelector('option:checked');
			setAttributes({ pageID: parseInt(selected.value) });
			event.preventDefault();
		};

		return wp.element.createElement(
			Fragment,
			null,
			wp.element.createElement(
				InspectorControls,
				null,
				wp.element.createElement(
					PanelBody,
					{ title: 'Select Parent' },
					wp.element.createElement(
						'select',
						{ value: pageID, onChange: setPage },
						wp.element.createElement(
							'option',
							{ value: '0' },
							'Select Page'
						),
						pageOptionsHtml
					)
				)
			),
			wp.element.createElement(ServerSideRender, {
				block: 'mdlr/dynamic-block-subpages',
				attributes: { pageID: pageID }
			})
		);
	}),

	save: function save(props) {
		var pageID = props.attributes.pageID,
		    setAttributes = props.setAttributes;

		return null;
	}
});

/***/ })
/******/ ]);