/**
 * Editor registration for rehab/intake-form.
 *
 * The form is fully server-rendered from assets/intake-spec.json (render.php);
 * the editor shows a static placeholder so the ~100-field wizard never loads
 * inside Gutenberg.
 */
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, Placeholder } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';
import './style.scss';

registerBlockType( metadata.name, {
	edit: ( { attributes, setAttributes } ) => {
		const blockProps = useBlockProps();
		return (
			<div { ...blockProps }>
				<InspectorControls>
					<PanelBody title={ __( 'Intake form', 'rehab-blocks' ) }>
						<TextControl
							label={ __( 'Heading', 'rehab-blocks' ) }
							value={ attributes.heading }
							onChange={ ( v ) => setAttributes( { heading: v } ) }
						/>
						<TextControl
							label={ __( 'Anchor id', 'rehab-blocks' ) }
							value={ attributes.anchorId }
							onChange={ ( v ) => setAttributes( { anchorId: v } ) }
						/>
					</PanelBody>
				</InspectorControls>
				<Placeholder
					icon="clipboard"
					label={ __( 'Intake Form', 'rehab-blocks' ) }
					instructions={ __(
						'Multi-step admissions questionnaire (7 steps, conditional fields, e-signature). Renders on the front end from assets/intake-spec.json.',
						'rehab-blocks'
					) }
				/>
			</div>
		);
	},
	save: () => null,
} );
