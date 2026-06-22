import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { Button, PanelBody, TextControl, TextareaControl } from '@wordpress/components';
import metadata from './block.json';

const blankFaq = { question: '', answer: '' };
const blankCategory = () => ( { key: '', label: '', faqs: [] } );

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const a = attributes;
		const set = ( k ) => ( v ) => setAttributes( { [ k ]: v } );
		const categories = a.categories || [];

		const setCategory = ( ci, key ) => ( v ) => {
			const next = categories.map( ( c, idx ) => ( idx === ci ? { ...c, [ key ]: v } : c ) );
			setAttributes( { categories: next } );
		};
		const setFaq = ( ci, faqIdx, key ) => ( v ) => {
			const next = categories.map( ( c, idx ) => {
				if ( idx !== ci ) {
					return c;
				}
				const faqs = ( c.faqs || [] ).map( ( f, j ) => ( j === faqIdx ? { ...f, [ key ]: v } : f ) );
				return { ...c, faqs };
			} );
			setAttributes( { categories: next } );
		};
		const addCategory = () => setAttributes( { categories: [ ...categories, blankCategory() ] } );
		const removeCategory = ( ci ) => setAttributes( { categories: categories.filter( ( _, idx ) => idx !== ci ) } );
		const addFaq = ( ci ) => {
			const next = categories.map( ( c, idx ) => ( idx === ci ? { ...c, faqs: [ ...( c.faqs || [] ), { ...blankFaq } ] } : c ) );
			setAttributes( { categories: next } );
		};
		const removeFaq = ( ci, faqIdx ) => {
			const next = categories.map( ( c, idx ) => ( idx === ci ? { ...c, faqs: ( c.faqs || [] ).filter( ( _, j ) => j !== faqIdx ) } : c ) );
			setAttributes( { categories: next } );
		};

		const blockProps = useBlockProps( { className: 'drt-faq drt-bg-cream drt-section' } );

		return (
			<>
				<InspectorControls>
					{ categories.map( ( cat, ci ) => (
						<PanelBody key={ ci } title={ `Category ${ ci + 1 }${ cat.label ? ': ' + cat.label : '' }` } initialOpen={ false }>
							<TextControl label="Tab label" value={ cat.label } onChange={ setCategory( ci, 'label' ) } />
							<TextControl label="Tab key (slug — used in data-* / panel id)" value={ cat.key } onChange={ setCategory( ci, 'key' ) } />
							{ ( cat.faqs || [] ).map( ( faq, faqIdx ) => (
								<div key={ faqIdx } style={ { borderTop: '1px solid #e5e5e5', marginTop: '0.75rem', paddingTop: '0.5rem' } }>
									<strong>{ `FAQ ${ faqIdx + 1 }` }</strong>
									<TextControl label="Question" value={ faq.question } onChange={ setFaq( ci, faqIdx, 'question' ) } />
									<TextareaControl label="Answer" value={ faq.answer } onChange={ setFaq( ci, faqIdx, 'answer' ) } rows={ 4 } />
									<Button isDestructive variant="link" onClick={ () => removeFaq( ci, faqIdx ) }>Remove FAQ</Button>
								</div>
							) ) }
							<div style={ { marginTop: '0.75rem' } }>
								<Button variant="primary" onClick={ () => addFaq( ci ) }>Add FAQ</Button>{ ' ' }
								<Button isDestructive variant="secondary" onClick={ () => removeCategory( ci ) }>Remove category</Button>
							</div>
						</PanelBody>
					) ) }
					<PanelBody title="Categories" initialOpen={ categories.length === 0 }>
						<Button variant="primary" onClick={ addCategory }>Add category</Button>
					</PanelBody>
				</InspectorControls>
				<section { ...blockProps }>
					<div className="drt-container drt-container--narrow">
						<RichText tagName="h2" className="drt-heading drt-heading--lg" value={ a.heading } onChange={ set( 'heading' ) } placeholder="Frequently Asked Questions" allowedFormats={ [] } />
						<div data-drt-tabs="faq">
							<nav className="drt-tabs__nav drt-tabs__nav--horizontal" role="tablist" aria-label="FAQ categories">
								{ categories.map( ( cat, ci ) => (
									<button key={ ci } className={ `drt-tabs__trigger${ ci === 0 ? ' is-active' : '' }` } type="button">{ cat.label || `Category ${ ci + 1 }` }</button>
								) ) }
							</nav>
							{ categories.map( ( cat, ci ) => (
								<div key={ ci } className={ `drt-tabs__panel${ ci === 0 ? ' is-active' : '' }` }>
									<div className="drt-faq__list">
										{ ( cat.faqs || [] ).map( ( faq, faqIdx ) => (
											<div className="drt-accordion__item" key={ faqIdx }>
												<button className="drt-accordion__trigger" aria-expanded="true" type="button">
													<span className="drt-accordion__trigger-text">{ faq.question || `Question ${ faqIdx + 1 }` }</span>
													<span className="drt-accordion__trigger-icon" aria-hidden="true"></span>
												</button>
												<div className="drt-accordion__content is-open">
													<p>{ faq.answer }</p>
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
