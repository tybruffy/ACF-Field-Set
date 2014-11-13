<?php

class acf_field_field_set extends acf_field {
	
	
	/*
	*  __construct
	*
	*  This function will setup the field type data
	*
	*  @type	function
	*  @date	5/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function __construct() {
		$this->defaults = array(
			'sub_fields'	=> array(),
			'layout' 		=> 'table',
		);


		/*
		*  name (string) Single word, no spaces. Underscores allowed
		*/
		
		$this->name = 'field_set';
		
		
		/*
		*  label (string) Multiple words, can include spaces, visible when selecting a field type
		*/
		
		$this->label = __('Field Set', 'acf-field_set');
		
		
		/*
		*  category (string) basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME
		*/
		
		$this->category = 'layout';
				
		// do not delete!
    	parent::__construct();
    	
	}
	
	
	/*
	*  render_field_settings()
	*
	*  Create extra settings for your field. These are visible when editing a field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field (array) the $field being edited
	*  @return	n/a
	*/
	
	function render_field_settings( $field ) {

		// vars
		$args = array(
			'fields'	=> $field['sub_fields'],
			'layout'	=> $field['layout'],
			'parent'	=> $field['ID']
		);
		
		
		?>
		<tr class="acf-field" data-setting="repeater" data-name="sub_fields">
			<td class="acf-label">
				<label><?php _e("Sub Fields",'acf'); ?></label>
				<p class="description"></p>		
			</td>
			<td class="acf-input">
				<?php 
				
				acf_get_view('field-group-fields', $args);
				
				?>
			</td>
		</tr>
		<?php

		acf_render_field_setting( $field, array(
			'label'			=> __('Layout','acf'),
			'instructions'	=> '',
			'class'			=> 'acf-repeater-layout',
			'type'			=> 'radio',
			'name'			=> 'layout',
			'layout'		=> 'horizontal',
			'choices'		=> array(
				'table'			=> __('Table','acf'),
				'block'			=> __('Block','acf'),
				'row'			=> __('Row','acf')
			)
		));

	}
	
	
	
	/*
	*  render_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field (array) the $field being rendered
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field (array) the $field being edited
	*  @return	n/a
	*/
	
	function render_field( $field ) {
		
		// ensure value is an array
		if( empty($field['value']) ) {
			$field['value'] = array();		
		}
				
		// field wrap
		$el            = 'td';
		$before_fields = '';
		$after_fields  = '';
		
		if( $field['layout'] == 'row' ) {
			$el = 'tr';
			$before_fields = '<td class="acf-table-wrap"><table class="acf-table">';
			$after_fields = '</table></td>';
		} elseif( $field['layout'] == 'block' ) {
			$el = 'div';
			
			$before_fields = '<td class="acf-fields">';
			$after_fields = '</td>';
		}
		
		// hidden input
		acf_hidden_input(array(
			'type'	=> 'hidden',
			'name'	=> $field['name'],
		));
		
?>
<div <?php acf_esc_attr_e(array( 'class' => 'acf-repeater' )); ?>>
<table <?php acf_esc_attr_e(array( 'class' => "acf-table acf-input-table {$field['layout']}-layout" )); ?>>
	
	<?php if( $field['layout'] == 'table' ): ?>
		<thead>
			<tr>	
			<?php foreach( $field['sub_fields'] as $sub_field ): 
				
				$atts = array(
					'class'		=> "acf-th acf-th-{$sub_field['name']}",
					'data-key'	=> $sub_field['key'],
				);
							
				// Add custom width
				if( $sub_field['wrapper']['width'] ) {			
					$atts['data-width'] = $sub_field['wrapper']['width'];
				}
					
				?>
				
				<th <?php acf_esc_attr_e( $atts ); ?>>
					<?php acf_the_field_label( $sub_field ); ?>
					<?php if( $sub_field['instructions'] ): ?>
						<p class="description"><?php echo $sub_field['instructions']; ?></p>
					<?php endif; ?>
				</th>
				
			<?php endforeach; ?>
			</tr>
		</thead>
	<?php endif; ?>
	
	<tbody>
		<tr class="acf-row">
			<?php echo $before_fields; ?>
			
			<?php foreach( $field['sub_fields'] as $sub_field ): 
				
				// add value
				if( isset($field["value"][$sub_field['key']]) ) {
					$sub_field['value'] = $field["value"][$sub_field['key']];
				} elseif( isset($sub_field['default_value']) ) {
					// no value, but this sub field has a default value
					$sub_field['value'] = $sub_field['default_value'];
				}
								
				// update prefix to allow for nested values
				$sub_field['prefix'] = "{$field['name']}";
				
				// render input
				acf_render_field_wrap( $sub_field, $el ); ?>
				
			<?php endforeach; ?>
			
			<?php echo $after_fields; ?>
		</tr>
	</tbody>
</table>
</div>
<?php
	}
	
		
	/*
	*  input_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
	*  Use this action to add CSS + JavaScript to assist your render_field() action.
	*
	*  @type	action (admin_enqueue_scripts)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/

	/*
	
	function input_admin_enqueue_scripts() {
		
		$dir = plugin_dir_url( __FILE__ );
		
		
		// register & include JS
		wp_register_script( 'acf-input-field_set', "{$dir}js/input.js" );
		wp_enqueue_script('acf-input-field_set');
		
		
		// register & include CSS
		wp_register_style( 'acf-input-field_set', "{$dir}css/input.css" ); 
		wp_enqueue_style('acf-input-field_set');
		
		
	}
	
	*/
	
	
	/*
	*  input_admin_head()
	*
	*  This action is called in the admin_head action on the edit screen where your field is created.
	*  Use this action to add CSS and JavaScript to assist your render_field() action.
	*
	*  @type	action (admin_head)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/

	/*
		
	function input_admin_head() {
	
		
		
	}
	
	*/
	
	
	/*
   	*  input_form_data()
   	*
   	*  This function is called once on the 'input' page between the head and footer
   	*  There are 2 situations where ACF did not load during the 'acf/input_admin_enqueue_scripts' and 
   	*  'acf/input_admin_head' actions because ACF did not know it was going to be used. These situations are
   	*  seen on comments / user edit forms on the front end. This function will always be called, and includes
   	*  $args that related to the current screen such as $args['post_id']
   	*
   	*  @type	function
   	*  @date	6/03/2014
   	*  @since	5.0.0
   	*
   	*  @param	$args (array)
   	*  @return	n/a
   	*/
   	
   	/*
   	
   	function input_form_data( $args ) {
	   	
		
	
   	}
   	
   	*/
	
	
	/*
	*  input_admin_footer()
	*
	*  This action is called in the admin_footer action on the edit screen where your field is created.
	*  Use this action to add CSS and JavaScript to assist your render_field() action.
	*
	*  @type	action (admin_footer)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/

	/*
		
	function input_admin_footer() {
	
		
		
	}
	
	*/
	
	
	/*
	*  field_group_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is edited.
	*  Use this action to add CSS + JavaScript to assist your render_field_options() action.
	*
	*  @type	action (admin_enqueue_scripts)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/

	/*
	
	function field_group_admin_enqueue_scripts() {
		
	}
	
	*/

	
	/*
	*  field_group_admin_head()
	*
	*  This action is called in the admin_head action on the edit screen where your field is edited.
	*  Use this action to add CSS and JavaScript to assist your render_field_options() action.
	*
	*  @type	action (admin_head)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/

	/*
	
	function field_group_admin_head() {
	
	}
	
	*/


	/*
	*  load_value()
	*
	*  This filter is applied to the $value after it is loaded from the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value (mixed) the value found in the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*  @return	$value
	*/
	
	function load_value( $value, $post_id, $field ) {
		// bail early if no value
		if( empty($value) || empty($field['sub_fields']) ) {
			return $value;
		}
		
		// vars
		$new_value = array();

		// loop through sub fields
		foreach( array_keys($field['sub_fields']) as $j ) {				
			// get sub field
			$sub_field = $field['sub_fields'][ $j ];
			
			// update $sub_field name
			$sub_field['name'] = "{$field['name']}_{$sub_field['name']}";

			// get value
			$sub_value = acf_get_value( $post_id, $sub_field );
		
			// add value
			$new_value[ $sub_field['key'] ] = $sub_value;
			
		}
		// foreach

		// return
		return $new_value;
	}
	
	
	/*
	*  update_value()
	*
	*  This filter is applied to the $value before it is saved in the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value (mixed) the value found in the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*  @return	$value
	*/
	
	function update_value( $value, $post_id, $field ) {
		if( !empty($value) ) {
			foreach( $field['sub_fields'] as $sub_field ) {
				$name      = "{$field['name']}_{$sub_field['name']}";
				$sub_value = $value[ $sub_field['key'] ];

				$sub_field['name'] = $name;
				acf_update_value( $sub_value, $post_id, $sub_field );
			}
			return true;
		}

		return false;
	}
	
	
	/*
	*  format_value()
	*
	*  This filter is appied to the $value after it is loaded from the db and before it is returned to the template
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value (mixed) the value which was loaded from the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*
	*  @return	$value (mixed) the modified value
	*/
	function format_value( $value, $post_id, $field ) {
		// bail early if no value
		if( empty($value) || empty($field['sub_fields']) ) {				
			return $value;
		}
						
		// loop through sub fields
		foreach( array_keys($field['sub_fields']) as $j ) {
			// get sub field
			$sub_field = $field['sub_fields'][ $j ];
			
			// extract value
			$sub_value = acf_extract_var( $value, $sub_field['key'] );
				
			// format value
			$sub_value = acf_format_value( $sub_value, $post_id, $sub_field );
		
			// append to $row
			$value[ $sub_field['name'] ] = $sub_value;	
		}
		
		// return
		return $value;
	}
	
	
	/*
	*  validate_value()
	*
	*  This filter is used to perform validation on the value prior to saving.
	*  All values are validated regardless of the field's required setting. This allows you to validate and return
	*  messages to the user if the value is not correct
	*
	*  @type	filter
	*  @date	11/02/2014
	*  @since	5.0.0
	*
	*  @param	$valid (boolean) validation status based on the value and the field's required setting
	*  @param	$value (mixed) the $_POST value
	*  @param	$field (array) the field array holding all the field options
	*  @param	$input (string) the corresponding input name for $_POST value
	*  @return	$valid
	*/
	function validate_value( $valid, $value, $field, $input ) {			
		// valid
		if( $field['required'] && empty($value) ) {
			$valid = false;
		}
			
		// check sub fields
		if( !empty($field['sub_fields']) && !$this->all_fields_empty($value, $field) ) {
			$this->validate_sub_fields($valid, $value, $field, $input);
		}
		
		return $valid;	
	}

	function all_fields_empty($value, $field) {
		$all_empty = true;
		foreach( $field['sub_fields'] as $sub_field ) {
			$k = $sub_field['key'];
			if ($sub_field["required"] && !empty($value[ $k ])) {
				$all_empty = false;
			}
		}
		return $all_empty;
	}

	function validate_sub_fields($valid, $value, $field, $input) {
		$keys = array_keys($value);
		foreach( $keys as $i ) {	
			foreach( $field['sub_fields'] as $sub_field ) {
				
				// vars
				$k = $sub_field['key'];
	
				// test sub field exists
				if( !isset($value[ $k ]) ) {
					continue;
				}

				// validate
				acf_validate_value( $value[$k], $sub_field, "{$input}[{$k}]");
			}	
		}
	}
	
	
	/*
	*  delete_value()
	*
	*  This action is fired after a value has been deleted from the db.
	*  Please note that saving a blank value is treated as an update, not a delete
	*
	*  @type	action
	*  @date	6/03/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (mixed) the $post_id from which the value was deleted
	*  @param	$key (string) the $meta_key which the value was deleted
	*  @return	n/a
	*/
	
	/*
	
	function delete_value( $post_id, $key ) {
		
		
		
	}
	
	*/
	
	
	/*
	*  load_field()
	*
	*  This filter is applied to the $field after it is loaded from the database
	*
	*  @type	filter
	*  @date	23/01/2013
	*  @since	3.6.0	
	*
	*  @param	$field (array) the field array holding all the field options
	*  @return	$field
	*/
	function load_field( $field ) {
		$field['sub_fields'] = acf_get_fields( $field );
		return $field;		
	}	
	
	
	
	
	/*
	*  update_field()
	*
	*  This filter is applied to the $field before it is saved to the database
	*
	*  @type	filter
	*  @date	23/01/2013
	*  @since	3.6.0
	*
	*  @param	$field (array) the field array holding all the field options
	*  @return	$field
	*/
	function update_field( $field ) {
		unset($field['sub_fields']);
		return $field;
	}
	

	/*
	*  duplicate_field()
	*
	*  This filter is appied to the $field before it is duplicated and saved to the database.
	*  Duplicates subfields.
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$field - the modified field
	*/
	function duplicate_field( $field ) {
		$sub_fields = acf_extract_var( $field, 'sub_fields' );
		$field      = acf_update_field( $field );
		acf_duplicate_fields( $sub_fields, $field['ID'] );
		return $field;
	}	
	
	
	/*
	*  delete_field()
	*
	*  This action is fired after a field is deleted from the database.
	*  Deletes all the subfields.
	*
	*  @type	action
	*  @date	11/02/2014
	*  @since	5.0.0
	*
	*  @param	$field (array) the field array holding all the field options
	*  @return	n/a
	*/
	function delete_field( $field ) {
		if( !empty($field['sub_fields']) ) {	
			foreach( $field['sub_fields'] as $sub_field ) {	
				acf_delete_field( $sub_field['ID'] );		
			}		
		}	
	}	

	
	
}


// create field
new acf_field_field_set();

?>
