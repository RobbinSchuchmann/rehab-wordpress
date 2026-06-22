import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { Button, PanelBody, TextControl } from '@wordpress/components';
import metadata from './block.json';

const Stars = () => (
	<>
		{ [ 0, 1, 2, 3, 4 ].map( ( i ) => (
			<svg key={ i } viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true">
				<polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26" />
			</svg>
		) ) }
	</>
);

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const a = attributes;
		const set = ( k ) => ( v ) => setAttributes( { [ k ]: v } );
		const blockProps = useBlockProps( { className: 'drt-authority drt-bg-cream' } );
		const logos = a.logos || [];
		const setLogo = ( i, key ) => ( v ) => {
			const next = logos.map( ( l, idx ) => ( idx === i ? { ...l, [ key ]: v } : l ) );
			setAttributes( { logos: next } );
		};
		return (
			<>
				<InspectorControls>
					<PanelBody title="Trust pillars" initialOpen>
						<MediaUploadCheck>
							<MediaUpload onSelect={ ( m ) => setAttributes( { ministryImageUrl: m.url, ministryAlt: m.alt || a.ministryAlt } ) } allowedTypes={ [ 'image' ] } render={ ( { open } ) => <Button variant="secondary" onClick={ open }>{ a.ministryImageUrl ? 'Replace ministry logo' : 'Pick ministry logo' }</Button> } />
						</MediaUploadCheck>
						<TextControl label="Ministry logo alt" value={ a.ministryAlt } onChange={ set( 'ministryAlt' ) } />
						<TextControl label="Ministry tooltip" value={ a.ministryTooltip } onChange={ set( 'ministryTooltip' ) } />
						<TextControl label="Google stars aria-label" value={ a.googleStarsLabel } onChange={ set( 'googleStarsLabel' ) } />
						<TextControl label="Google tooltip" value={ a.googleTooltip } onChange={ set( 'googleTooltip' ) } />
						<TextControl label="Recovery.com stars aria-label" value={ a.recoveryStarsLabel } onChange={ set( 'recoveryStarsLabel' ) } />
						<TextControl label="Recovery.com tooltip" value={ a.recoveryTooltip } onChange={ set( 'recoveryTooltip' ) } />
					</PanelBody>
					<PanelBody title="Partner logos" initialOpen={ false }>
						{ logos.map( ( l, i ) => (
							<div key={ i } style={ { borderBottom: '1px solid #e5e5e5', marginBottom: '0.75rem', paddingBottom: '0.5rem' } }>
								<MediaUploadCheck>
									<MediaUpload onSelect={ ( m ) => setLogo( i, 'imageUrl' )( m.url ) } allowedTypes={ [ 'image' ] } render={ ( { open } ) => <Button variant="secondary" onClick={ open }>{ l.imageUrl ? 'Replace image' : 'Pick image' }</Button> } />
								</MediaUploadCheck>
								<TextControl label={ `Logo ${ i + 1 } alt` } value={ l.alt || '' } onChange={ setLogo( i, 'alt' ) } />
								<TextControl label={ `Logo ${ i + 1 } tooltip` } value={ l.tip || '' } onChange={ setLogo( i, 'tip' ) } />
								<Button isDestructive variant="link" onClick={ () => setAttributes( { logos: logos.filter( ( _, j ) => j !== i ) } ) }>Remove logo</Button>
							</div>
						) ) }
						<Button variant="primary" onClick={ () => setAttributes( { logos: [ ...logos, { imageUrl: '', alt: '', tip: '' } ] } ) }>Add logo</Button>
					</PanelBody>
				</InspectorControls>
				<section { ...blockProps }>
					<div className="drt-container">
						<div className="drt-authority__pillars">
							<div className="drt-authority__pillar" data-tooltip={ a.ministryTooltip }>
								{ a.ministryImageUrl ? (
									<img src={ a.ministryImageUrl } alt={ a.ministryAlt } className="drt-authority__ministry-logo drt-animate-luxury-pulse" width="64" height="64" />
								) : (
									<div className="drt-authority__ministry-logo" />
								) }
								<RichText tagName="span" className="drt-authority__pillar-text" value={ a.ministryText } onChange={ set( 'ministryText' ) } placeholder="Ministry text…" allowedFormats={ [] } />
							</div>

							<div className="drt-authority__divider" aria-hidden="true"></div>

							<div className="drt-authority__pillar" data-tooltip={ a.googleTooltip }>
								<div className="drt-authority__stars drt-animate-luxury-pulse" aria-label={ a.googleStarsLabel }><Stars /></div>
								<RichText tagName="span" className="drt-authority__pillar-text" value={ a.googleText } onChange={ set( 'googleText' ) } placeholder="Google text…" allowedFormats={ [] } />
							</div>

							<div className="drt-authority__divider" aria-hidden="true"></div>

							<div className="drt-authority__pillar" data-tooltip={ a.recoveryTooltip }>
								<div className="drt-authority__stars drt-animate-luxury-pulse" aria-label={ a.recoveryStarsLabel }><Stars /></div>
								<RichText tagName="span" className="drt-authority__pillar-text" value={ a.recoveryText } onChange={ set( 'recoveryText' ) } placeholder="Recovery.com text…" allowedFormats={ [] } />
							</div>
						</div>

						<div className="drt-authority__partners">
							{ logos.map( ( l, i ) => (
								<div className="drt-authority__partner" data-tooltip={ l.tip } key={ i }>
									{ l.imageUrl ? (
										<img src={ l.imageUrl } alt={ l.alt } className="drt-partner-logo" />
									) : (
										<div className="drt-authority__partner-placeholder"><span>{ l.alt || `Logo ${ i + 1 }` }</span></div>
									) }
								</div>
							) ) }
						</div>
					</div>
				</section>
			</>
		);
	},
	save: () => null,
} );
