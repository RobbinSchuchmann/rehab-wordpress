import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { Button, PanelBody, TextControl, TextareaControl } from '@wordpress/components';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const a = attributes;
		const set = ( k ) => ( v ) => setAttributes( { [ k ]: v } );
		const tabs = a.tabs || [];
		const setTab = ( i, key ) => ( v ) => {
			const next = tabs.map( ( t, idx ) => ( idx === i ? { ...t, [ key ]: v } : t ) );
			setAttributes( { tabs: next } );
		};
		const addTab = () =>
			setAttributes( { tabs: [ ...tabs, { id: 'tab-' + Date.now(), label: '', image: '', imageAlt: '', content: '' } ] } );
		const removeTab = ( i ) => setAttributes( { tabs: tabs.filter( ( _, j ) => j !== i ) } );
		const blockProps = useBlockProps( { className: 'drt-accommodation drt-bg-white drt-section' } );

		return (
			<>
				<InspectorControls>
					<PanelBody title="Accommodation tabs" initialOpen>
						{ tabs.map( ( t, i ) => (
							<div key={ i } style={ { borderBottom: '1px solid #e5e5e5', marginBottom: '0.75rem', paddingBottom: '0.5rem' } }>
								<TextControl label={ `Tab ${ i + 1 } id (slug)` } value={ t.id } onChange={ setTab( i, 'id' ) } />
								<TextControl label="Label" value={ t.label } onChange={ setTab( i, 'label' ) } />
								<MediaUploadCheck>
									<MediaUpload
										onSelect={ ( m ) => {
											const next = tabs.map( ( tt, idx ) =>
												idx === i ? { ...tt, image: m.url, imageAlt: tt.imageAlt || m.alt || '' } : tt
											);
											setAttributes( { tabs: next } );
										} }
										allowedTypes={ [ 'image' ] }
										render={ ( { open } ) => (
											<Button variant="secondary" onClick={ open }>{ t.image ? 'Replace image' : 'Pick image' }</Button>
										) }
									/>
								</MediaUploadCheck>
								<TextControl label="Image URL" value={ t.image } onChange={ setTab( i, 'image' ) } />
								<TextControl label="Image alt" value={ t.imageAlt } onChange={ setTab( i, 'imageAlt' ) } />
								<TextareaControl label="Content" value={ t.content } onChange={ setTab( i, 'content' ) } rows={ 3 } />
								<Button isDestructive variant="link" onClick={ () => removeTab( i ) }>Remove tab</Button>
							</div>
						) ) }
						<Button variant="primary" onClick={ addTab }>Add tab</Button>
					</PanelBody>
				</InspectorControls>
				<section { ...blockProps }>
					<div className="drt-container">
						<div className="drt-section-header">
							<RichText tagName="span" className="drt-eyebrow" value={ a.eyebrow } onChange={ set( 'eyebrow' ) } placeholder="Eyebrow (optional)…" allowedFormats={ [] } />
							<RichText tagName="h2" className="drt-heading drt-heading--lg" value={ a.heading } onChange={ set( 'heading' ) } placeholder="Heading…" allowedFormats={ [] } />
							<RichText tagName="p" className="drt-body drt-text-balance" value={ a.intro } onChange={ set( 'intro' ) } placeholder="Intro…" allowedFormats={ [ 'core/bold', 'core/italic' ] } />
						</div>

						<div className="drt-accommodation__layout" data-drt-tabs="accommodation">
							<div className="drt-accommodation__tabs">
								<nav className="drt-tabs__nav" role="tablist" aria-label="Accommodation areas">
									{ tabs.map( ( t, i ) => (
										<RichText
											key={ i }
											tagName="button"
											className={ `drt-tabs__trigger${ i === 0 ? ' is-active' : '' }` }
											value={ t.label }
											onChange={ setTab( i, 'label' ) }
											placeholder="Tab label…"
											allowedFormats={ [] }
										/>
									) ) }
								</nav>
							</div>
							<div className="drt-accommodation__content">
								{ tabs.map( ( t, i ) => (
									<div key={ i } className={ `drt-tabs__panel${ i === 0 ? ' is-active' : '' }` }>
										<div className="drt-accommodation__image-wrap">
											{ t.image ? (
												<img src={ t.image } alt={ t.imageAlt || t.label } className="drt-accommodation__image" loading="lazy" decoding="async" />
											) : (
												<div className="drt-accommodation__image" />
											) }
										</div>
										<div className="drt-accommodation__text">
											<h3 className="drt-heading drt-heading--md">{ t.label || 'Tab label' }</h3>
											<RichText tagName="p" className="drt-body" value={ t.content } onChange={ setTab( i, 'content' ) } placeholder="Content…" allowedFormats={ [ 'core/bold', 'core/italic' ] } />
										</div>
									</div>
								) ) }
							</div>
						</div>
					</div>
				</section>
			</>
		);
	},
	save: () => null,
} );
