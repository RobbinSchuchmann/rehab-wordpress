import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const a = attributes;
		const set = ( k ) => ( v ) => setAttributes( { [ k ]: v } );
		// Front end is position:fixed and hidden on desktop; in the editor we
		// preview it as a static, relatively-positioned bar so it stays visible.
		const blockProps = useBlockProps( {
			className: 'drt-mobile-sticky',
			style: { position: 'relative' },
		} );
		return (
			<>
				<InspectorControls>
					<PanelBody title="CTA link" initialOpen>
						<TextControl
							label="CTA href"
							value={ a.ctaHref }
							onChange={ set( 'ctaHref' ) }
							placeholder="/contact-us/"
						/>
						<TextControl
							label="Aria label"
							value={ a.ariaLabel }
							onChange={ set( 'ariaLabel' ) }
							placeholder="Check availability"
						/>
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<div className="drt-mobile-sticky__inner">
						<RichText
							tagName="a"
							className="drt-btn drt-btn--luxury drt-mobile-sticky__btn"
							value={ a.ctaLabel }
							onChange={ set( 'ctaLabel' ) }
							placeholder="CHECK AVAILABILITY"
							allowedFormats={ [] }
						/>
					</div>
				</div>
			</>
		);
	},
	save: () => null,
} );
