import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { Button, PanelBody, TextControl } from '@wordpress/components';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const a = attributes;
		const set = ( k ) => ( v ) => setAttributes( { [ k ]: v } );
		const blockProps = useBlockProps( { className: 'drt-founder drt-bg-white drt-section' } );
		return (
			<>
				<InspectorControls>
					<PanelBody title="Founder portrait" initialOpen>
						<MediaUploadCheck>
							<MediaUpload
								onSelect={ ( m ) => setAttributes( { imageUrl: m.url, imageAlt: m.alt || '' } ) }
								allowedTypes={ [ 'image' ] }
								render={ ( { open } ) => (
									<Button variant="secondary" onClick={ open }>
										{ a.imageUrl ? 'Replace image' : 'Pick image' }
									</Button>
								) }
							/>
						</MediaUploadCheck>
						<TextControl label="Image URL" value={ a.imageUrl } onChange={ set( 'imageUrl' ) } help="Leave blank to use the default founder portrait." />
						<TextControl label="Image alt" value={ a.imageAlt } onChange={ set( 'imageAlt' ) } />
					</PanelBody>
				</InspectorControls>
				<section { ...blockProps }>
					<div className="drt-container drt-container--narrow">
						<blockquote className="drt-founder__quote">
							<RichText tagName="p" value={ a.quote } onChange={ set( 'quote' ) } placeholder="Pull-quote…" allowedFormats={ [ 'core/italic' ] } />
						</blockquote>

						<div className="drt-founder__signature">
							{ a.imageUrl ? (
								<img src={ a.imageUrl } alt={ a.imageAlt } className="drt-founder__portrait" width="220" height="293" />
							) : (
								<div className="drt-founder__portrait" style={ { width: 220, height: 293, display: 'flex', alignItems: 'center', justifyContent: 'center', background: '#eee' } }>
									<span>{ a.imageAlt || 'Founder portrait' }</span>
								</div>
							) }
							<div className="drt-founder__bio">
								<RichText tagName="h2" className="drt-heading drt-heading--sm" value={ a.heading } onChange={ set( 'heading' ) } placeholder="Name, role…" allowedFormats={ [] } />
								<RichText tagName="p" className="drt-body" value={ a.bio } onChange={ set( 'bio' ) } placeholder="Bio…" allowedFormats={ [ 'core/bold', 'core/italic' ] } />
								<RichText tagName="p" className="drt-founder__mate-quote" value={ a.inspirationQuote } onChange={ set( 'inspirationQuote' ) } placeholder="Inspiration quote…" allowedFormats={ [ 'core/italic' ] } />
							</div>
						</div>
					</div>
				</section>
			</>
		);
	},
	save: () => null,
} );
