import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { Button, PanelBody, SelectControl, TextControl, TextareaControl } from '@wordpress/components';
import metadata from './block.json';
import './style.scss';

const EMPTY_CARD = { kick: '', title: '', items: [] };

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const blockProps = useBlockProps();
		const a = attributes;
		const set = ( k ) => ( v ) => setAttributes( { [ k ]: v } );
		const cards = a.cards || [];
		const setCard = ( i, key ) => ( v ) => {
			const next = cards.map( ( c, idx ) => ( idx === i ? { ...c, [ key ]: v } : c ) );
			setAttributes( { cards: next } );
		};
		return (
			<>
				<InspectorControls>
					<PanelBody title="Section" initialOpen>
						<SelectControl
							label="Background"
							value={ a.background }
							options={ [ { label: 'Cream', value: 'cream' }, { label: 'White', value: 'white' }, { label: 'Sage mist', value: 'sage-mist' } ] }
							onChange={ set( 'background' ) }
						/>
					</PanelBody>
					{ cards.map( ( card, i ) => (
						<PanelBody key={ i } title={ `Card ${ i + 1 } — ${ card.title || 'untitled' }` } initialOpen={ false }>
							<TextControl label="Kick (small label)" value={ card.kick } onChange={ setCard( i, 'kick' ) } />
							<TextControl label="Title" value={ card.title } onChange={ setCard( i, 'title' ) } />
							<TextareaControl
								label="Checklist items (one per line)"
								value={ ( card.items || [] ).join( '\n' ) }
								onChange={ ( v ) => setCard( i, 'items' )( v.split( '\n' ) ) }
								rows={ 6 }
							/>
							<Button isDestructive variant="secondary" onClick={ () => setAttributes( { cards: cards.filter( ( _, j ) => j !== i ) } ) }>Remove card</Button>
						</PanelBody>
					) ) }
					<PanelBody title="Cards" initialOpen={ false }>
						<Button variant="primary" onClick={ () => setAttributes( { cards: [ ...cards, { ...EMPTY_CARD } ] } ) }>Add card</Button>
					</PanelBody>
					<PanelBody title="Info panel (optional)" initialOpen={ false }>
						<TextControl label="Panel eyebrow" value={ a.panelEyebrow } onChange={ set( 'panelEyebrow' ) } />
						<TextControl label="Panel title" value={ a.panelTitle } onChange={ set( 'panelTitle' ) } />
						<TextareaControl label="Panel body" value={ a.panelBody } onChange={ set( 'panelBody' ) } rows={ 3 } />
						<TextareaControl
							label="Panel checklist (one per line)"
							value={ ( a.panelItems || [] ).join( '\n' ) }
							onChange={ ( v ) => setAttributes( { panelItems: v.split( '\n' ) } ) }
							rows={ 6 }
						/>
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<section className={ `rehab-checklist-cards rehab-bg-${ a.background }` }>
						<div className="rehab-container">
							<div className="rehab-checklist-cards__head">
								<RichText tagName="span" className="rehab-checklist-cards__eyebrow" value={ a.eyebrow } onChange={ set( 'eyebrow' ) } placeholder="Eyebrow…" allowedFormats={ [] } />
								<RichText tagName="h2" className="rehab-checklist-cards__heading" value={ a.heading } onChange={ set( 'heading' ) } placeholder="Heading…" allowedFormats={ [] } />
								<RichText tagName="p" className="rehab-checklist-cards__lede" value={ a.lede } onChange={ set( 'lede' ) } placeholder="Lede…" allowedFormats={ [] } />
							</div>
							<div className={ `rehab-checklist-cards__grid rehab-checklist-cards__grid--cols-${ Math.min( 4, Math.max( 1, cards.length ) ) }` }>
								{ cards.map( ( card, i ) => (
									<div className="rehab-checklist-card" key={ i }>
										<div className="rehab-checklist-card__icon">◆</div>
										<div className="rehab-checklist-card__kick">{ card.kick }</div>
										<h3 className="rehab-checklist-card__title">{ card.title }</h3>
										<ul className="rehab-checklist-card__list">
											{ ( card.items || [] ).filter( ( s ) => s.trim() !== '' ).map( ( item, j ) => <li key={ j }>✓ { item }</li> ) }
										</ul>
									</div>
								) ) }
							</div>
							{ a.panelTitle ? (
								<div className="rehab-checklist-cards__panel">
									<div className="rehab-checklist-cards__panel-copy">
										<span className="rehab-checklist-cards__eyebrow">{ a.panelEyebrow }</span>
										<h3>{ a.panelTitle }</h3>
										<p>{ a.panelBody }</p>
									</div>
									<ul className="rehab-checklist-cards__panel-list">
										{ ( a.panelItems || [] ).filter( ( s ) => s.trim() !== '' ).map( ( item, j ) => <li key={ j }>✓ { item }</li> ) }
									</ul>
								</div>
							) : null }
						</div>
					</section>
				</div>
			</>
		);
	},
	save: () => null,
} );
