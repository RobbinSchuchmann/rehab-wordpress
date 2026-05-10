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
	const { background, label, phoneText, phoneNumber } = attributes;
	const blockProps = useBlockProps( {
		className: `rehab-phone-cta rehab-phone-cta--bg-${ background }`,
	} );
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Phone CTA', 'rehab-blocks' ) } initialOpen>
					<TextControl
						label={ __( 'Phone number for tel: link', 'rehab-blocks' ) }
						value={ phoneNumber }
						onChange={ ( v ) => setAttributes( { phoneNumber: v } ) }
						help={ __( 'Numeric only with country code, e.g. +6620568987', 'rehab-blocks' ) }
					/>
					<SelectControl
						label={ __( 'Background', 'rehab-blocks' ) }
						value={ background }
						options={ [
							{ label: 'Sage mist', value: 'sage-mist' },
							{ label: 'Charcoal (dark)', value: 'charcoal' },
							{ label: 'White', value: 'white' },
							{ label: 'Cream', value: 'cream' },
						] }
						onChange={ ( v ) => setAttributes( { background: v } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<aside { ...blockProps }>
				<div className="rehab-container">
					<div className="rehab-phone-cta__inner">
						<RichText
							tagName="span"
							className="rehab-phone-cta__label"
							value={ label }
							onChange={ ( v ) => setAttributes( { label: v } ) }
							placeholder={ __( 'Label text', 'rehab-blocks' ) }
							allowedFormats={ [] }
						/>
						<RichText
							tagName="span"
							className="rehab-phone-cta__number"
							value={ phoneText }
							onChange={ ( v ) => setAttributes( { phoneText: v } ) }
							placeholder={ __( 'Display phone', 'rehab-blocks' ) }
							allowedFormats={ [] }
						/>
					</div>
				</div>
			</aside>
		</>
	);
}

function save( { attributes } ) {
	const { background, label, phoneText, phoneNumber } = attributes;
	const blockProps = useBlockProps.save( {
		className: `rehab-phone-cta rehab-phone-cta--bg-${ background }`,
	} );
	const tel = phoneNumber.replace( /[^0-9+]/g, '' );
	return (
		<aside { ...blockProps }>
			<div className="rehab-container">
				<div className="rehab-phone-cta__inner">
					{ label && (
						<RichText.Content
							tagName="span"
							className="rehab-phone-cta__label"
							value={ label }
						/>
					) }
					<a className="rehab-phone-cta__number" href={ `tel:${ tel }` }>
						<RichText.Content value={ phoneText } />
					</a>
				</div>
			</div>
		</aside>
	);
}

registerBlockType( metadata.name, { edit: Edit, save } );
