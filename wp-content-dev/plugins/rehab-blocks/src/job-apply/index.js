import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl } from '@wordpress/components';
import metadata from './block.json';
import './style.scss';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const blockProps = useBlockProps();
		const a = attributes;
		const set = ( k ) => ( v ) => setAttributes( { [ k ]: v } );
		return (
			<>
				<InspectorControls>
					<PanelBody title="Apply box" initialOpen>
						<SelectControl
							label="Background"
							value={ a.background }
							options={ [ { label: 'Cream', value: 'cream' }, { label: 'White', value: 'white' }, { label: 'Sage mist', value: 'sage-mist' } ] }
							onChange={ set( 'background' ) }
						/>
						<TextControl label="Application e-mail" value={ a.email } onChange={ set( 'email' ) } help="The mailto target; the subject line is prefilled with the job title." />
						<TextControl label="Button text" value={ a.buttonText } onChange={ set( 'buttonText' ) } />
					</PanelBody>
				</InspectorControls>
				<section { ...blockProps } className={ `${ blockProps.className || '' } rehab-job-apply rehab-bg-${ a.background }` }>
					<div className="rehab-container">
						<div className="rehab-job-apply__card">
							<RichText tagName="span" className="rehab-eyebrow" value={ a.eyebrow } onChange={ set( 'eyebrow' ) } placeholder="Eyebrow…" allowedFormats={ [] } />
							<RichText tagName="h2" className="rehab-job-apply__heading" value={ a.heading } onChange={ set( 'heading' ) } placeholder="Apply for this position" allowedFormats={ [] } />
							<RichText tagName="p" className="rehab-job-apply__body" value={ a.body } onChange={ set( 'body' ) } placeholder="Instructions…" allowedFormats={ [] } />
							<span className="rehab-btn rehab-btn--luxury rehab-job-apply__btn">{ a.buttonText || 'Apply by email' }</span>
							<p className="rehab-job-apply__email">{ a.email }</p>
							<RichText tagName="p" className="rehab-job-apply__helper" value={ a.helper } onChange={ set( 'helper' ) } placeholder="Helper line…" allowedFormats={ [] } />
						</div>
					</div>
				</section>
			</>
		);
	},
	save() {
		return null; // dynamic — rendered by render.php
	},
} );
