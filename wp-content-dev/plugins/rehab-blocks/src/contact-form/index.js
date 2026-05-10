import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import {
	useBlockProps,
	RichText,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl } from '@wordpress/components';
import metadata from './block.json';
import './style.scss';
import './editor.scss';

function Edit( { attributes, setAttributes } ) {
	const { background, heading, subheading, shortcode } = attributes;
	const blockProps = useBlockProps( {
		className: `rehab-contact-form rehab-bg-${ background }`,
	} );
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Contact form', 'rehab-blocks' ) } initialOpen>
					<TextControl
						label={ __( 'Form shortcode', 'rehab-blocks' ) }
						value={ shortcode }
						onChange={ ( v ) => setAttributes( { shortcode: v } ) }
						help={ __( 'Forminator: [forminator_form id="X"], or any shortcode that outputs a form.', 'rehab-blocks' ) }
					/>
					<SelectControl
						label={ __( 'Background', 'rehab-blocks' ) }
						value={ background }
						options={ [
							{ label: 'White', value: 'white' },
							{ label: 'Cream', value: 'cream' },
							{ label: 'Sage mist', value: 'sage-mist' },
						] }
						onChange={ ( v ) => setAttributes( { background: v } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<section { ...blockProps }>
				<div className="rehab-container rehab-container--narrow">
					<header className="rehab-contact-form__header">
						<RichText
							tagName="h2"
							className="rehab-heading rehab-heading--lg"
							value={ heading }
							onChange={ ( v ) => setAttributes( { heading: v } ) }
							placeholder={ __( 'Heading', 'rehab-blocks' ) }
							allowedFormats={ [ 'core/bold', 'core/italic' ] }
						/>
						<RichText
							tagName="p"
							className="rehab-contact-form__subheading"
							value={ subheading }
							onChange={ ( v ) => setAttributes( { subheading: v } ) }
							placeholder={ __( 'Subheading…', 'rehab-blocks' ) }
						/>
					</header>
					<div className="rehab-contact-form__placeholder">
						<code>{ shortcode || '[forminator_form id="X"]' }</code>
						<p style={ { marginTop: '0.5rem', opacity: 0.7, fontSize: '0.8125rem' } }>
							{ __( 'Form will render here on the live page.', 'rehab-blocks' ) }
						</p>
					</div>
				</div>
			</section>
		</>
	);
}

function save( { attributes } ) {
	const { background, heading, subheading, shortcode } = attributes;
	const blockProps = useBlockProps.save( {
		className: `rehab-contact-form rehab-bg-${ background }`,
	} );
	return (
		<section { ...blockProps }>
			<div className="rehab-container rehab-container--narrow">
				<header className="rehab-contact-form__header">
					{ heading && (
						<RichText.Content
							tagName="h2"
							className="rehab-heading rehab-heading--lg"
							value={ heading }
						/>
					) }
					{ subheading && (
						<RichText.Content
							tagName="p"
							className="rehab-contact-form__subheading"
							value={ subheading }
						/>
					) }
				</header>
				<div className="rehab-contact-form__embed">{ shortcode }</div>
			</div>
		</section>
	);
}

registerBlockType( metadata.name, { edit: Edit, save } );
