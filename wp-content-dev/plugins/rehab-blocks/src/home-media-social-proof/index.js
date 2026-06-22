import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { Button, PanelBody, TextControl, TextareaControl } from '@wordpress/components';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const a = attributes;
		const logos = a.logos || [];
		const setLogo = ( i, key ) => ( v ) => {
			const next = logos.map( ( l, idx ) => ( idx === i ? { ...l, [ key ]: v } : l ) );
			setAttributes( { logos: next } );
		};
		const blockProps = useBlockProps( { className: 'drt-media drt-bg-cream' } );
		return (
			<>
				<InspectorControls>
					<PanelBody title="Media logos" initialOpen>
						{ logos.map( ( logo, i ) => (
							<div key={ i } style={ { borderBottom: '1px solid #e5e5e5', marginBottom: '0.75rem', paddingBottom: '0.5rem' } }>
								<MediaUploadCheck>
									<MediaUpload
										onSelect={ ( m ) => setLogo( i, 'src' )( m.url ) }
										allowedTypes={ [ 'image' ] }
										render={ ( { open } ) => (
											<Button variant="secondary" onClick={ open }>{ logo.src ? 'Replace logo' : 'Pick logo' }</Button>
										) }
									/>
								</MediaUploadCheck>
								<TextControl label={ `Logo ${ i + 1 } URL` } value={ logo.src || '' } onChange={ setLogo( i, 'src' ) } />
								<TextControl label="Alt text" value={ logo.alt || '' } onChange={ setLogo( i, 'alt' ) } />
								<TextareaControl label="Tooltip" value={ logo.tip || '' } onChange={ setLogo( i, 'tip' ) } rows={ 3 } />
								<Button isDestructive variant="link" onClick={ () => setAttributes( { logos: logos.filter( ( _, j ) => j !== i ) } ) }>Remove logo</Button>
							</div>
						) ) }
						<Button variant="primary" onClick={ () => setAttributes( { logos: [ ...logos, { src: '', alt: '', tip: '' } ] } ) }>Add logo</Button>
					</PanelBody>
				</InspectorControls>
				<section { ...blockProps }>
					<div className="drt-container">
						<div className="drt-media__grid">
							{ logos.map( ( logo, i ) => (
								<div className="drt-media__item" data-tooltip={ logo.tip || '' } key={ i }>
									{ logo.src ? (
										<img src={ logo.src } alt={ logo.alt || '' } className="drt-partner-logo" loading="lazy" />
									) : (
										<span className="drt-partner-logo">{ logo.alt || `Logo ${ i + 1 }` }</span>
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
