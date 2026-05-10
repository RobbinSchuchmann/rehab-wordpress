import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	RichText,
	InspectorControls,
	URLInput,
} from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';

export default function Edit( { attributes, setAttributes } ) {
	const {
		variant,
		background,
		heading,
		body,
		buttonText,
		buttonUrl,
		helper,
	} = attributes;

	const blockProps = useBlockProps( {
		className: `rehab-cta rehab-cta--${ variant } rehab-bg-${ background }`,
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'CTA settings', 'rehab-blocks' ) } initialOpen>
					<SelectControl
						label={ __( 'Size', 'rehab-blocks' ) }
						value={ variant }
						options={ [
							{ label: 'Default (with body text)', value: 'default' },
							{ label: 'Compact (heading + button only)', value: 'compact' },
						] }
						onChange={ ( v ) => setAttributes( { variant: v } ) }
					/>
					<SelectControl
						label={ __( 'Background', 'rehab-blocks' ) }
						value={ background }
						options={ [
							{ label: 'Sage mist', value: 'sage-mist' },
							{ label: 'White', value: 'white' },
							{ label: 'Cream', value: 'cream' },
						] }
						onChange={ ( v ) => setAttributes( { background: v } ) }
					/>
					<p style={ { marginTop: '1em', fontSize: '12px', opacity: 0.7 } }>
						{ __( 'Button URL', 'rehab-blocks' ) }
					</p>
					<URLInput
						value={ buttonUrl }
						onChange={ ( v ) => setAttributes( { buttonUrl: v } ) }
					/>
				</PanelBody>
			</InspectorControls>

			<section { ...blockProps }>
				<div className={ `rehab-container ${ variant === 'compact' ? 'rehab-container--text' : 'rehab-container--narrow' }` }>
					<div className="rehab-cta__inner">
						<RichText
							tagName="h2"
							className={ `rehab-heading ${ variant === 'compact' ? 'rehab-heading--md' : 'rehab-heading--lg' }` }
							value={ heading }
							onChange={ ( v ) => setAttributes( { heading: v } ) }
							placeholder={ __( 'CTA heading…', 'rehab-blocks' ) }
							allowedFormats={ [ 'core/bold', 'core/italic' ] }
						/>
						{ variant === 'default' && (
							<RichText
								tagName="p"
								className="rehab-cta__body"
								value={ body }
								onChange={ ( v ) => setAttributes( { body: v } ) }
								placeholder={ __( 'Body text…', 'rehab-blocks' ) }
							/>
						) }
						<RichText
							tagName="span"
							className="rehab-btn rehab-btn--luxury"
							value={ buttonText }
							onChange={ ( v ) => setAttributes( { buttonText: v } ) }
							placeholder={ __( 'Button text', 'rehab-blocks' ) }
							allowedFormats={ [] }
						/>
						<RichText
							tagName="p"
							className="rehab-cta__helper"
							value={ helper }
							onChange={ ( v ) => setAttributes( { helper: v } ) }
							placeholder={ __( 'Helper text…', 'rehab-blocks' ) }
							allowedFormats={ [] }
						/>
					</div>
				</div>
			</section>
		</>
	);
}
