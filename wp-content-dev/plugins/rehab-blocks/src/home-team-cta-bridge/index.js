import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const a = attributes;
		const set = ( k ) => ( v ) => setAttributes( { [ k ]: v } );
		const blockProps = useBlockProps( { className: 'drt-team-cta drt-bg-sage-mist' } );
		return (
			<>
				<InspectorControls>
					<PanelBody title="Button" initialOpen>
						<TextControl label="Button text" value={ a.buttonText } onChange={ set( 'buttonText' ) } />
						<TextControl label="Button URL" value={ a.buttonUrl } onChange={ set( 'buttonUrl' ) } />
					</PanelBody>
				</InspectorControls>
				<section { ...blockProps }>
					<div className="drt-container drt-container--text">
						<div className="drt-team-cta__inner">
							<RichText tagName="h2" className="drt-heading drt-heading--md drt-text-balance" value={ a.heading } onChange={ set( 'heading' ) } placeholder="Heading…" allowedFormats={ [] } />
							<RichText tagName="p" className="drt-body" value={ a.body } onChange={ set( 'body' ) } placeholder="Body…" allowedFormats={ [ 'core/bold', 'core/italic' ] } />
							<a href={ a.buttonUrl || '#' } className="drt-btn drt-btn--luxury" onClick={ ( e ) => e.preventDefault() }>
								<RichText tagName="span" value={ a.buttonText } onChange={ set( 'buttonText' ) } placeholder="Button text…" allowedFormats={ [] } />
							</a>
							<RichText tagName="p" className="drt-team-cta__helper" value={ a.helper } onChange={ set( 'helper' ) } placeholder="Helper line…" allowedFormats={ [] } />
						</div>
					</div>
				</section>
			</>
		);
	},
	save: () => null,
} );
