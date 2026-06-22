import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const a = attributes;
		const set = ( k ) => ( v ) => setAttributes( { [ k ]: v } );
		const blockProps = useBlockProps( { className: 'drt-location drt-bg-white drt-section' } );
		return (
			<>
				<InspectorControls>
					<PanelBody title="Map" initialOpen>
						<TextControl label="Map embed URL" value={ a.mapSrc } onChange={ set( 'mapSrc' ) } />
						<TextControl label="Map iframe title" value={ a.mapTitle } onChange={ set( 'mapTitle' ) } />
					</PanelBody>
				</InspectorControls>
				<section { ...blockProps }>
					<div className="drt-container drt-container--narrow">
						<div className="drt-location__grid">
							<div className="drt-location__text">
								<RichText tagName="h2" className="drt-heading drt-heading--lg" value={ a.heading } onChange={ set( 'heading' ) } placeholder="Heading…" allowedFormats={ [] } />
								<div className="drt-location__details">
									<RichText tagName="p" className="drt-location__name" value={ a.placeName } onChange={ set( 'placeName' ) } placeholder="Place name…" allowedFormats={ [] } />
									<RichText tagName="address" className="drt-body drt-location__address" value={ a.address } onChange={ set( 'address' ) } placeholder="Address (Shift+Enter for line breaks)…" allowedFormats={ [] } />
								</div>
							</div>
							<div className="drt-location__map">
								{ a.mapSrc ? (
									<iframe src={ a.mapSrc } className="drt-location__iframe" loading="lazy" referrerPolicy="no-referrer-when-downgrade" title={ a.mapTitle } allowFullScreen />
								) : (
									<div className="drt-location__iframe" style={ { display: 'flex', alignItems: 'center', justifyContent: 'center', background: '#eef0ee', minHeight: '300px' } }>
										<span>Set a map embed URL in the sidebar</span>
									</div>
								) }
							</div>
						</div>
					</div>
				</section>
			</>
		);
	},
	save: () => null,
} );
