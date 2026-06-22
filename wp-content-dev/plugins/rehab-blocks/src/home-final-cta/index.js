import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl } from '@wordpress/components';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const a = attributes;
		const set = ( k ) => ( v ) => setAttributes( { [ k ]: v } );
		const blockProps = useBlockProps( { className: `drt-final-cta drt-bg-${ a.background }` } );
		return (
			<>
				<InspectorControls>
					<PanelBody title="Background" initialOpen>
						<SelectControl
							label="Background"
							value={ a.background }
							options={ [
								{ label: 'Sage mist', value: 'sage-mist' },
								{ label: 'White', value: 'white' },
								{ label: 'Cream', value: 'cream' },
							] }
							onChange={ set( 'background' ) }
						/>
					</PanelBody>
					<PanelBody title="Button" initialOpen>
						<TextControl label="Button URL" value={ a.buttonUrl } onChange={ set( 'buttonUrl' ) } />
					</PanelBody>
				</InspectorControls>
				<section { ...blockProps }>
					<div className="drt-container drt-container--narrow">
						<div className="drt-final-cta__inner">
							<RichText tagName="h2" className="drt-heading drt-heading--lg drt-text-balance" value={ a.heading } onChange={ set( 'heading' ) } placeholder="Heading…" allowedFormats={ [] } />
							<RichText tagName="p" className="drt-body" value={ a.body } onChange={ set( 'body' ) } placeholder="Body…" allowedFormats={ [ 'core/bold', 'core/italic' ] } />
							<RichText tagName="a" className="drt-btn drt-btn--luxury" value={ a.buttonText } onChange={ set( 'buttonText' ) } placeholder="Button text…" allowedFormats={ [] } />
							<RichText tagName="p" className="drt-final-cta__helper" value={ a.helper } onChange={ set( 'helper' ) } placeholder="Helper line…" allowedFormats={ [] } />
						</div>
					</div>
				</section>
			</>
		);
	},
	save: () => null,
} );
