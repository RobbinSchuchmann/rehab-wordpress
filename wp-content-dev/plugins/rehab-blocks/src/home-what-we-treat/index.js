import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { Button, PanelBody, TextControl } from '@wordpress/components';
import metadata from './block.json';

const blankCard = { title: '', desc: '', image: '', alt: '', href: '' };
const blankCategory = () => ( { key: '', label: '', cards: [] } );

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const a = attributes;
		const set = ( k ) => ( v ) => setAttributes( { [ k ]: v } );
		const categories = a.categories || [];

		const setCategory = ( ci, key ) => ( v ) => {
			const next = categories.map( ( c, idx ) => ( idx === ci ? { ...c, [ key ]: v } : c ) );
			setAttributes( { categories: next } );
		};
		const setCard = ( ci, cardIdx, key ) => ( v ) => {
			const next = categories.map( ( c, idx ) => {
				if ( idx !== ci ) {
					return c;
				}
				const cards = ( c.cards || [] ).map( ( card, j ) => ( j === cardIdx ? { ...card, [ key ]: v } : card ) );
				return { ...c, cards };
			} );
			setAttributes( { categories: next } );
		};
		const addCategory = () => setAttributes( { categories: [ ...categories, blankCategory() ] } );
		const removeCategory = ( ci ) => setAttributes( { categories: categories.filter( ( _, idx ) => idx !== ci ) } );
		const addCard = ( ci ) => {
			const next = categories.map( ( c, idx ) => ( idx === ci ? { ...c, cards: [ ...( c.cards || [] ), { ...blankCard } ] } : c ) );
			setAttributes( { categories: next } );
		};
		const removeCard = ( ci, cardIdx ) => {
			const next = categories.map( ( c, idx ) => ( idx === ci ? { ...c, cards: ( c.cards || [] ).filter( ( _, j ) => j !== cardIdx ) } : c ) );
			setAttributes( { categories: next } );
		};

		const blockProps = useBlockProps( { className: 'drt-treat drt-bg-white drt-section--lg' } );

		return (
			<>
				<InspectorControls>
					{ categories.map( ( cat, ci ) => (
						<PanelBody key={ ci } title={ `Category ${ ci + 1 }${ cat.label ? ': ' + cat.label : '' }` } initialOpen={ false }>
							<TextControl label="Tab label" value={ cat.label } onChange={ setCategory( ci, 'label' ) } />
							<TextControl label="Tab key (slug — used in data-* / panel id)" value={ cat.key } onChange={ setCategory( ci, 'key' ) } />
							{ ( cat.cards || [] ).map( ( card, cardIdx ) => (
								<div key={ cardIdx } style={ { borderTop: '1px solid #e5e5e5', marginTop: '0.75rem', paddingTop: '0.5rem' } }>
									<strong>{ `Card ${ cardIdx + 1 }` }</strong>
									<TextControl label="Title" value={ card.title } onChange={ setCard( ci, cardIdx, 'title' ) } />
									<TextControl label="Description" value={ card.desc } onChange={ setCard( ci, cardIdx, 'desc' ) } />
									<TextControl label="Link URL" value={ card.href } onChange={ setCard( ci, cardIdx, 'href' ) } />
									<TextControl label="Image alt" value={ card.alt } onChange={ setCard( ci, cardIdx, 'alt' ) } />
									<MediaUploadCheck>
										<MediaUpload
											onSelect={ ( m ) => {
												setCard( ci, cardIdx, 'image' )( m.url );
												if ( m.alt ) {
													setCard( ci, cardIdx, 'alt' )( m.alt );
												}
											} }
											allowedTypes={ [ 'image' ] }
											render={ ( { open } ) => <Button variant="secondary" onClick={ open }>{ card.image ? 'Replace image' : 'Pick image' }</Button> }
										/>
									</MediaUploadCheck>
									<TextControl label="Image URL" value={ card.image } onChange={ setCard( ci, cardIdx, 'image' ) } />
									<Button isDestructive variant="link" onClick={ () => removeCard( ci, cardIdx ) }>Remove card</Button>
								</div>
							) ) }
							<div style={ { marginTop: '0.75rem' } }>
								<Button variant="primary" onClick={ () => addCard( ci ) }>Add card</Button>{ ' ' }
								<Button isDestructive variant="secondary" onClick={ () => removeCategory( ci ) }>Remove category</Button>
							</div>
						</PanelBody>
					) ) }
					<PanelBody title="Categories" initialOpen={ categories.length === 0 }>
						<Button variant="primary" onClick={ addCategory }>Add category</Button>
					</PanelBody>
				</InspectorControls>
				<section { ...blockProps }>
					<div className="drt-container">
						<div className="drt-section-header">
							<RichText tagName="h2" className="drt-heading drt-heading--lg drt-text-balance" value={ a.heading } onChange={ set( 'heading' ) } placeholder="Heading…" allowedFormats={ [] } />
							<RichText tagName="p" className="drt-body" value={ a.intro } onChange={ set( 'intro' ) } placeholder="Intro…" allowedFormats={ [ 'core/bold', 'core/italic' ] } />
						</div>
						<div data-drt-tabs="treat">
							<nav className="drt-tabs__nav drt-tabs__nav--horizontal" role="tablist" aria-label="Treatment categories">
								{ categories.map( ( cat, ci ) => (
									<button key={ ci } className={ `drt-tabs__trigger${ ci === 0 ? ' is-active' : '' }` } type="button">{ cat.label || `Category ${ ci + 1 }` }</button>
								) ) }
							</nav>
							{ categories.map( ( cat, ci ) => (
								<div key={ ci } className={ `drt-tabs__panel${ ci === 0 ? ' is-active' : '' }` }>
									<div className="drt-treat__grid">
										{ ( cat.cards || [] ).map( ( card, cardIdx ) => (
											<div key={ cardIdx } className="drt-card--treatment">
												<div className="drt-card--treatment__image">
													{ card.image ? <img src={ card.image } alt={ card.alt } /> : <div className="drt-card--treatment__placeholder"><span>{ card.alt || 'Image' }</span></div> }
												</div>
												<div className="drt-card--treatment__body">
													<RichText tagName="h3" className="drt-card--treatment__title" value={ card.title } onChange={ setCard( ci, cardIdx, 'title' ) } placeholder="Card title…" allowedFormats={ [] } />
													<RichText tagName="p" className="drt-card--treatment__desc" value={ card.desc } onChange={ setCard( ci, cardIdx, 'desc' ) } placeholder="Card description…" allowedFormats={ [] } />
												</div>
											</div>
										) ) }
									</div>
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
