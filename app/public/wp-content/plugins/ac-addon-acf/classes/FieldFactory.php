<?php

namespace ACA\ACF;

use ACA\ACF\Field\Checkbox;
use ACA\ACF\Field\ColorPicker;
use ACA\ACF\Field\DatePicker;
use ACA\ACF\Field\DateTimePicker;
use ACA\ACF\Field\Email;
use ACA\ACF\Field\File;
use ACA\ACF\Field\FlexibleContent;
use ACA\ACF\Field\Gallery;
use ACA\ACF\Field\GoogleMap;
use ACA\ACF\Field\Group;
use ACA\ACF\Field\Image;
use ACA\ACF\Field\ImageCrop;
use ACA\ACF\Field\Link;
use ACA\ACF\Field\Number;
use ACA\ACF\Field\Oembed;
use ACA\ACF\Field\PageLink;
use ACA\ACF\Field\Password;
use ACA\ACF\Field\PostObject;
use ACA\ACF\Field\Radio;
use ACA\ACF\Field\Range;
use ACA\ACF\Field\Relationship;
use ACA\ACF\Field\Repeater;
use ACA\ACF\Field\Select;
use ACA\ACF\Field\Taxonomy;
use ACA\ACF\Field\Text;
use ACA\ACF\Field\Textarea;
use ACA\ACF\Field\TimePicker;
use ACA\ACF\Field\TrueFalse;
use ACA\ACF\Field\Url;
use ACA\ACF\Field\User;
use ACA\ACF\Field\Wysiwyg;

class FieldFactory {

	public function create( $type, Column $column ) {

		switch ( $type ) {
			case '' :
				return new Field( $column );
			case FieldType::TYPE_BUTTON_GROUP :
				return new Select( $column );
			case FieldType::TYPE_CHECKBOX :
				return new Checkbox( $column );
			case FieldType::TYPE_COLOR_PICKER :
				return new ColorPicker( $column );
			case FieldType::TYPE_DATE_PICKER :
				return new DatePicker( $column );
			case FieldType::TYPE_DATE_TIME_PICKER :
				return new DateTimePicker( $column );
			case FieldType::TYPE_EMAIL :
				return new Email( $column );
			case FieldType::TYPE_FILE :
				return new File( $column );
			case FieldType::TYPE_FLEXIBLE_CONTENT :
				return new FlexibleContent( $column );
			case FieldType::TYPE_GOOLGE_MAP :
				return new GoogleMap( $column );
			case FieldType::TYPE_GROUP :
				return new Group( $column );
			case FieldType::TYPE_GALLERY :
				return new Gallery( $column );
			case FieldType::TYPE_IMAGE :
				return new Image( $column );
			case FieldType::TYPE_IMAGE_CROP :
				return new ImageCrop( $column );
			case FieldType::TYPE_LINK :
				return new Link( $column );
			case FieldType::TYPE_NUMBER :
				return new Number( $column );
			case FieldType::TYPE_OEMBED :
				return new Oembed( $column );
			case FieldType::TYPE_PAGE_LINK :
				return new PageLink( $column );
			case FieldType::TYPE_PASSWORD :
				return new Password( $column );
			case FieldType::TYPE_POST :
				return new PostObject( $column );
			case FieldType::TYPE_RADIO :
				return new Radio( $column );
			case FieldType::TYPE_RANGE :
				return new Range( $column );
			case FieldType::TYPE_RELATIONSHIP :
				return new Relationship( $column );
			case FieldType::TYPE_REPEATER :
				return new Repeater( $column );
			case FieldType::TYPE_SELECT :
				return new Select( $column );
			case FieldType::TYPE_TAXONOMY :
				return new Taxonomy( $column );
			case FieldType::TYPE_TEXT :
				return new Text( $column );
			case FieldType::TYPE_TEXTAREA :
				return new Textarea( $column );
			case FieldType::TYPE_TIME_PICKER :
				return new TimePicker( $column );
			case FieldType::TYPE_BOOLEAN :
				return new TrueFalse( $column );
			case FieldType::TYPE_URL :
				return new Url( $column );
			case FieldType::TYPE_USER :
				return new User( $column );
			case FieldType::TYPE_WYSIWYG :
				return new Wysiwyg( $column );
			case FieldType::TYPE_TAB :
			case FieldType::TYPE_MESSAGE :
			case FieldType::TYPE_SEPARATOR :
			case FieldType::TYPE_OUTPUT :
			default :
				$field = new Field\Unsupported( $column );

				return apply_filters( 'acp/acf/field', $field, $type, $column );
		}
	}

}